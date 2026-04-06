<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TuristicPlace;
use App\Models\reviews;
use App\Models\PlaceEvent;
use App\Http\Controllers\Api\TuristicPlaceApiController;
use App\Http\Controllers\Api\ReviewApiController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\CommentController;   
require_once __DIR__.'/countries_api.php';

Route::get('/health', function() {
    return response()->json(['status' => 'ok']);
});

// DEV: Quick Mailtrap test endpoint (remove in production)
Route::get('/dev/test-mail', function() {
    Mail::raw('Prueba de Mailtrap desde EcoRisaralda', function ($message) {
        $message->to('test@example.com')
                ->subject('Test Mailtrap');
    });
    return response()->json(['sent' => true]);
});

// DEV: List users from current database connection (SQLite by default)
Route::get('/dev/users', function() {
    return DB::table('users')->select('id','name','email','email_verified_at','created_at')->orderBy('id','desc')->get();
});

// DEV: Normalize roles to MySQL enum values
Route::get('/dev/fix-roles', function() {
    $countTurist = DB::table('users')->where('role','turist')->update(['role' => 'user']);
    $countNull = DB::table('users')->whereNull('role')->orWhere('role','')->update(['role' => 'user']);
    return response()->json(['turist_to_user' => $countTurist, 'null_to_user' => $countNull]);
});

// ============ PUBLIC ENDPOINTS ============
Route::get('/preferences', function () {
    if (\App\Models\preference::count() === 0) {
        $defaults = [
            ['name' => 'Senderismo', 'image' => 'hiking', 'color' => 'FF6B6B'],
            ['name' => 'Avistamiento de aves', 'image' => 'birdwatching', 'color' => 'FFA500'],
            ['name' => 'Ciclismo de montaña', 'image' => 'biking', 'color' => '4ECDC4'],
            ['name' => 'Escalada o rappel', 'image' => 'climbing', 'color' => 'FFD93D'],
            ['name' => 'Fauna y voluntariado', 'image' => 'wildlife', 'color' => '6BCB77'],
            ['name' => 'Reservas naturales', 'image' => 'reserves', 'color' => '8B6F47'],
            ['name' => 'Kayak o canoa', 'image' => 'kayaking', 'color' => '4D96FF'],
            ['name' => 'Baños de bosque', 'image' => 'forest_bathing', 'color' => '52B788'],
        ];

        foreach ($defaults as $item) {
            \App\Models\preference::firstOrCreate(
                ['name' => $item['name']],
                ['image' => $item['image'], 'color' => $item['color']]
            );
        }
    }

    return \App\Models\preference::all();
});

// Servir archivos desde storage (avatares, imágenes, etc)
Route::get('/files/{type}/{filename}', function ($type, $filename) {
    // 1. Buscar en storage/app/public/ (para imágenes subidas por sistema)
    $storagePath = "{$type}/{$filename}";
    
    if (Storage::disk('public')->exists($storagePath)) {
        return response()->file(storage_path("app/public/{$storagePath}"));
    }
    
    // 2. Buscar en public/ directamente (avatares, imágenes subidas por usuarios)
    $publicPath = public_path("{$type}/{$filename}");
    if (file_exists($publicPath)) {
        return response()->file($publicPath);
    }
    
    // 3. Buscar en seeders con el tipo exacto
    $seedPath = public_path("seeders/images/places/{$type}/{$filename}");
    if (file_exists($seedPath)) {
        return response()->file($seedPath);
    }
    
    // 4. Si es plural, intentar el singular
    if (substr($type, -1) === 's') {
        $typeSingular = substr($type, 0, -1);
        $seedPath = public_path("seeders/images/places/{$typeSingular}/{$filename}");
        if (file_exists($seedPath)) {
            return response()->file($seedPath);
        }
    }
    
    // 5. Si es singular, intentar el plural
    if (substr($type, -1) !== 's') {
        $typePlural = $type . 's';
        $seedPath = public_path("seeders/images/places/{$typePlural}/{$filename}");
        if (file_exists($seedPath)) {
            return response()->file($seedPath);
        }
    }
    
    // Archivo no encontrado
    return response()->json(['message' => 'Archivo no encontrado: ' . $type . '/' . $filename], 404);
})->where('filename', '.*');

// Rutas públicas de eventos (para todos, logueados o no)
Route::get('/events/upcoming', function (Request $request) {
    $expiredEvents = PlaceEvent::where('starts_at', '<', now())->get();
    foreach ($expiredEvents as $expiredEvent) {
        if ($expiredEvent->image) {
            Storage::disk('public')->delete($expiredEvent->image);
        }
        $expiredEvent->delete();
    }
    $limit = (int) $request->query('limit', 5);
    $limit = $limit > 0 ? $limit : 5;

    $events = PlaceEvent::with('place:id,name')
        ->where('starts_at', '>=', now())
        ->where('approval_status', 'approved')
        ->orderBy('starts_at', 'asc')
        ->limit($limit * 2) // Traer más para filtrar
        ->get()
        ->filter(function ($event) {
            // Solo mostrar eventos cuyo sitio existe y está aprobado
            return $event->place && (!isset($event->place->approval_status) || $event->place->approval_status === 'approved');
        })
        ->values()
        ->take($limit);

    return response()->json([
        'events' => $events,
    ]);
});

// Estas rutas necesitan sesión pero omiten CSRF para el primer contacto del cliente SPA
Route::middleware('web')->group(function () {
    // Notificaciones de usuario (turista)
    Route::middleware('auth')->group(function () {
        Route::get('/user/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::post('/user/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markRead']);
        Route::post('/user/notifications/{id}/archive', [\App\Http\Controllers\Api\NotificationController::class, 'archive']);
        Route::post('/user/notifications/archive-all', [\App\Http\Controllers\Api\NotificationController::class, 'archiveAll']);
    });
    // Turistic places - read only (public but session-aware)
    Route::get('/places', [TuristicPlaceApiController::class, 'index']);
    Route::get('/places/{id}', [TuristicPlaceApiController::class, 'show']);
    
    Route::post('/register', function (Request $request) {
        if (Auth::guard('web')->check() || Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Ya has iniciado sesión.'], 403);
        }
        
        $data = $request->validate([
            'name' => 'required|string|min:2|max:50',
            'last_name' => 'required|string|min:2|max:50',
            'email' => [
                'required',
                'email',
                function($attribute, $value, $fail) {
                    if (\App\Models\User::where('email', $value)->exists()) {
                        $fail('El correo ya está registrado o pertenece a una cuenta desactivada.');
                    }
                }
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:64',
                'regex:/[a-z]/',      // al menos una minúscula
                'regex:/[A-Z]/',      // al menos una mayúscula
                'regex:/[0-9]/',      // al menos un dígito
            ],
            'role' => 'required|in:turist,operator,user,admin',
            'country' => 'nullable|integer|exists:countries,id',
            'birth_date' => 'nullable|date|before:-16 years',
        ], [
            'birth_date.before' => 'Debes ser mayor de 16 años para registrarte',
            'password.min' => 'La contraseña debe tener entre 8 y 64 caracteres',
            'password.max' => 'La contraseña debe tener entre 8 y 64 caracteres',
            'password.regex' => 'La contraseña debe incluir al menos una mayúscula, una minúscula y un dígito',
            'name.min' => 'El nombre debe tener al menos 2 caracteres',
            'name.max' => 'El nombre no debe tener más de 50 caracteres',
            'last_name.required' => 'El apellido es obligatorio',
            'last_name.min' => 'El apellido debe tener al menos 2 caracteres',
            'last_name.max' => 'El apellido no debe tener más de 50 caracteres',
        ]);

        $role = $data['role'];
        if ($role === 'turist') { $role = 'user'; }
        if (! in_array($role, ['user','operator','admin'])) { $role = 'user'; }

        $user = User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $role,
            'country_id' => $data['country'] ?? null,
            'date_of_birth' => $data['birth_date'] ?? null,
        ]);

        if (method_exists($user, 'sendEmailVerificationNotification')) {
            $user->sendEmailVerificationNotification();
        }

        return response()->json([
            'message' => 'Registro exitoso. Revisa tu correo para verificar la cuenta.',
        ]);
    });

    Route::post('/login', function (Request $request) {
        if (Auth::guard('web')->check() || Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Ya has iniciado sesión.'], 403);
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Verificar si el email está verificado
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return response()->json([
                    'message' => 'Debes verificar tu correo electrónico antes de iniciar sesión. Revisa tu bandeja de entrada.',
                ], 403);
            }
            
            $request->session()->regenerate();
            
            // Generar Sanctum token para SPA
            $token = $user->createToken('api-token')->plainTextToken;
            
            $userData = $user->toArray();
            if ($user->image) {
                $imagePath = str_replace('\\', '/', $user->image);
                $userData['avatar_url'] = url('/api/files/' . $imagePath);
            }
            
            return response()->json([
                'user' => $userData,
                'token' => $token,
                'message' => 'Inicio de sesión exitoso',
            ]);
        }

        return response()->json([
            'message' => 'Credenciales incorrectas',
        ], 401);
    });

    Route::post('/email/verification-notification', function (Request $request) {
        $request->validate(['email' => 'required|email']);
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            // Retorna un éxito genérico para evitar enumeración de cuentas
            return response()->json(['message' => 'Enlace de verificación enviado']);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'El correo ya está verificado']);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Enlace de verificación enviado']);
    })->middleware('throttle:6,1');

    Route::post('/forgot-password', function (Request $request) {
        if (Auth::guard('web')->check() || Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Ya has iniciado sesión.'], 403);
        }
        
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __('passwords.sent')]);
        }

        return response()->json(['message' => __('passwords.user')], 422);
    });

    Route::post('/reset-password', function (Request $request) {
        if (Auth::guard('web')->check() || Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Ya has iniciado sesión.'], 403);
        }

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __('passwords.reset')]);
        }

        return response()->json(['message' => __('passwords.token')], 422);
    });
});

// ============ AUTHENTICATED ROUTES ============
Route::middleware(['web', 'auth:sanctum'])->group(function () {
        Route::post('/logout', function (Request $request) {
            // Revocar todos los tokens Sanctum del usuario
            try {
                $request->user()->tokens()->delete();
            } catch (\Exception $e) {
                // Ignorar si no hay tokens
            }
            
            // Cerrar sesión web si existe
            try {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            } catch (\Exception $e) {
                // Ignorar errores de sesión
            }
            
            return response()->json(['message' => 'Sesión cerrada']);
        });

        // Obtener usuario actual (alias para /profile)
        Route::get('/user', function (Request $request) {
            $user = $request->user();
            $userData = $user->toArray();
            if ($user->image) {
                $imagePath = str_replace('\\', '/', $user->image);
                $userData['avatar_url'] = url('/api/files/' . $imagePath);
            }
            return response()->json($userData);
        });

        // Perfil: obtener perfil actual
        Route::get('/profile', function (Request $request) {
            $user = $request->user();
            $userData = $user->toArray();
            // Agregar URL completa del avatar si existe
            if ($user->image) {
                $imagePath = str_replace('\\', '/', $user->image);
                $userData['avatar_url'] = url('/api/files/' . $imagePath);
            }
            return response()->json($userData);
        });

        // Perfil: actualizar datos básicos
        Route::put('/profile', function (Request $request) {
            $user = $request->user();
            $data = $request->validate([
                'name' => 'required|string|min:2|max:50',
                'last_name' => 'nullable|string|min:2|max:50',
                'email' => 'required|email|unique:users,email,'.$user->id,
            ], [
                'name.min' => 'El nombre debe tener al menos 2 caracteres',
                'name.max' => 'El nombre no debe tener más de 50 caracteres',
                'last_name.min' => 'El apellido debe tener al menos 2 caracteres',
                'last_name.max' => 'El apellido no debe tener más de 50 caracteres',
            ]);

            $user->name = $data['name'];
            $user->last_name = $data['last_name'] ?? null;
            $user->email = $data['email'];
            $user->save();

            // Incluir avatar_url en la respuesta
            $userData = $user->toArray();
            if ($user->image) {
                $imagePath = str_replace('\\', '/', $user->image);
                $userData['avatar_url'] = url('/api/files/' . $imagePath);
            }

            return response()->json(['user' => $userData, 'message' => 'Perfil actualizado']);
        });

        // Perfil: cambiar contraseña
        Route::post('/profile/password', function (Request $request) {
            $user = $request->user();
            $data = $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if (! Hash::check($data['current_password'], $user->password)) {
                return response()->json(['message' => 'La contraseña actual es incorrecta'], 422);
            }

            $user->password = Hash::make($data['password']);
            $user->setRememberToken(Str::random(60));
            $user->save();


            // Forzar logout y login para asegurar que la sesión use el nuevo hash de contraseña
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            Auth::guard('web')->login($user);
            $request->session()->regenerate();

            return response()->json(['message' => 'Contraseña actualizada']);
        });

        // Perfil: eliminar cuenta
        Route::post('/profile/delete', function (Request $request) {
            $user = $request->user();
            $data = $request->validate([
                'current_password' => 'required|string',
            ]);

            if (! Hash::check($data['current_password'], $user->password)) {
                return response()->json(['message' => 'La contraseña actual es incorrecta'], 422);
            }

            // Solo permitir eliminación total para turistas
            if ($user->role !== 'user') {
                return response()->json(['message' => 'Solo los turistas pueden eliminar completamente su cuenta desde el perfil.'], 403);
            }

            // Eliminar favoritos
            $user->favoritePlaces()->detach();
            // Eliminar reseñas
            if (method_exists($user, 'reviews')) {
                $user->reviews()->delete();
            } else {
                \App\Models\reviews::where('user_id', $user->id)->delete();
            }
            // Eliminar sitios turísticos creados
            if (method_exists($user, 'turisticPlaces')) {
                $user->turisticPlaces()->delete();
            } else {
                \App\Models\TuristicPlace::where('user_id', $user->id)->delete();
            }
            // Eliminar eventos creados
            if (method_exists($user, 'events')) {
                $user->events()->delete();
            } // else: no acción, ya que place_events no tiene user_id

            try {
                $user->tokens()->delete();
            } catch (\Exception $e) {}
            try {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            } catch (\Exception $e) {}

            $user->delete();
            return response()->json(['message' => 'Cuenta eliminada permanentemente.']);
        });

        // Perfil: subir foto
        Route::post('/profile/avatar', function (Request $request) {
            try {
                // Validar primero
                $validated = $request->validate([
                    'avatar' => 'required|image|mimes:jpeg,png,jpg,webp,gif,heic,heif|max:2048',
                ]);

                $user = $request->user();
                if (!$user) {
                    return response()->json(['message' => 'No autenticado'], 401);
                }
                
                // Eliminar imagen anterior
                if ($user->image && $user->image !== 'null') {
                    try {
                        Storage::disk('public')->delete($user->image);
                    } catch (\Exception $e) {
                        // Ignorar error al eliminar
                    }
                }
                
                // Generar nombre
                $ext = $request->file('avatar')->guessExtension();
                $filename = 'avatar_' . $user->id . '_' . time() . '.' . $ext;
                $relativePath = 'avatars/' . $filename;
                
                // Guardar usando stream (más eficiente)
                $resource = fopen($request->file('avatar')->getRealPath(), 'r');
                $saved = Storage::disk('public')->writeStream($relativePath, $resource);
                if (is_resource($resource)) {
                    fclose($resource);
                }
                
                if (!$saved) {
                    return response()->json(['message' => 'Error al guardar archivo'], 500);
                }
                
                // Actualizar usuario
                $user->image = $relativePath;
                $user->save();
                
                return response()->json([
                    'message' => 'Foto actualizada',
                    'avatar_url' => url('/api/files/' . $relativePath),
                    'user' => $user,
                ], 200);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json(['errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
            }
        });

        // Perfil: eliminar foto (restaurar avatar por defecto)
        Route::delete('/profile/avatar', function (Request $request) {
            $user = $request->user();

            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            $user->image = null;
            $user->save();

            $userData = $user->toArray();
            $userData['avatar_url'] = null;

            return response()->json([
                'message' => 'Foto eliminada',
                'avatar_url' => null,
                'user' => $userData,
            ]);
        });



        Route::get('/user', function (Request $request) {
            $user = $request->user();
            $userData = $user->toArray();
            // Agregar URL completa del avatar si existe
            if ($user->image) {
                $imagePath = str_replace('\\', '/', $user->image);
                $userData['avatar_url'] = url('/api/files/' . $imagePath);
            }
            return response()->json($userData);
        });

        // ============ USER PREFERENCES ============
        Route::get('/user/preferences', function (Request $request) {
            return $request->user()->preferences()->get();
        });
        Route::post('/user/preferences', function (Request $request) {
            $validated = $request->validate([
                'preferences' => 'required|array|min:1',
                'preferences.*' => 'integer|exists:preferences,id',
            ]);
            $request->user()->preferences()->sync($validated['preferences']);
            // Marcar que ya pasó por preferencias
            $request->user()->update(['first_time_preferences' => false]);
            return response()->json(['message' => 'Preferencias actualizadas']);
        });
        Route::get('/user/first-time-preferences', function (Request $request) {
            return response()->json([
                'first_time' => $request->user()->first_time_preferences
            ]);
        });

        // Recomendaciones basadas en preferencias del usuario
        Route::get('/recommendations', function (Request $request) {
            $user = $request->user();
            $preferenceIds = $user ? $user->preferences()->pluck('preferences.id')->toArray() : [];

            if (count($preferenceIds) === 0) {
                return response()->json([]);
            }

            $places = TuristicPlace::with('label')
                ->where('approval_status', 'approved')
                ->whereHas('label', function ($query) use ($preferenceIds) {
                    $query->whereIn('preferences.id', $preferenceIds);
                })
                ->latest()
                ->take(12)
                ->get();

            return response()->json($places);
        });

        // ============ FAVORITOS ============
        Route::get('/favorites', function (Request $request) {
            return $request->user()->favoritePlaces()->get();
        });

        Route::post('/places/{id}/favorite', function (Request $request, $id) {
            $place = TuristicPlace::findOrFail($id);
            $request->user()->favoritePlaces()->syncWithoutDetaching([$place->id]);

            return response()->json(['message' => 'Agregado a favoritos']);
        });

        Route::delete('/places/{id}/favorite', function (Request $request, $id) {
            $request->user()->favoritePlaces()->detach($id);
            return response()->json(['message' => 'Eliminado de favoritos']);
        });

        // ============ HISTORIAL (TURISTA) ============
        Route::post('/places/{id}/visit', function (Request $request, $id) {
            $user = $request->user();
            $place = TuristicPlace::findOrFail($id);
            $now = now();

            $exists = DB::table('user_place_visits')
                ->where('user_id', $user->id)
                ->where('place_id', $place->id)
                ->exists();

            if ($exists) {
                DB::table('user_place_visits')
                    ->where('user_id', $user->id)
                    ->where('place_id', $place->id)
                    ->update([
                        'visited_at' => $now,
                        'updated_at' => $now,
                    ]);
            } else {
                DB::table('user_place_visits')->insert([
                    'user_id' => $user->id,
                    'place_id' => $place->id,
                    'visited_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            return response()->json(['message' => 'Visita registrada']);
        });

        Route::get('/user/history', function (Request $request) {
            $limit = (int) $request->query('limit', 8);
            $limit = $limit > 0 ? $limit : 8;

            $items = DB::table('user_place_visits')
                ->where('user_place_visits.user_id', $request->user()->id)
                ->join('turistic_places', 'user_place_visits.place_id', '=', 'turistic_places.id')
                ->select(
                    'user_place_visits.id',
                    'user_place_visits.place_id',
                    'user_place_visits.visited_at',
                    'turistic_places.name as place_name',
                    'turistic_places.localization as place_localization'
                )
                ->orderByDesc('user_place_visits.visited_at')
                ->limit($limit)
                ->get()
                ->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'visited_at' => $row->visited_at,
                        'place' => [
                            'id' => $row->place_id,
                            'name' => $row->place_name,
                            'localization' => $row->place_localization,
                        ],
                    ];
                });

            return response()->json($items);
        });

        Route::get('/user/reviews', function (Request $request) {
            $limit = (int) $request->query('limit', 8);
            $limit = $limit > 0 ? $limit : 8;

            $items = reviews::with(['place:id,name'])
                ->where('user_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json($items);
        });

        Route::get('/events/next', function () {
            $expiredEvents = PlaceEvent::where('starts_at', '<', now())->get();
            foreach ($expiredEvents as $expiredEvent) {
                if ($expiredEvent->image) {
                    Storage::disk('public')->delete($expiredEvent->image);
                }
                $expiredEvent->delete();
            }
            $event = PlaceEvent::with('place:id,name')
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at', 'asc')
                ->first();

            return response()->json([
                'event' => $event,
            ]);
        });



            // Obtener evento por ID
            Route::get('/events/{id}', function (Request $request, $id) {
                $event = PlaceEvent::with('place:id,name')->find($id);
                if (!$event) {
                    return response()->json([
                        'message' => 'El evento no está disponible en este momento.',
                        'event' => null,
                        'place' => null,
                    ], 404);
                }
                return response()->json([
                    'event' => $event,
                    'place' => $event->place,
                ]);
            });

            // Editar evento
            Route::put('/events/{id}', function (Request $request, $id) {
                $event = PlaceEvent::findOrFail($id);
                $data = $request->validate([
                    'title' => 'required|string|max:255',
                    'description' => 'nullable|string|max:1000',
                    'starts_at' => 'required|date',
                    'ends_at' => 'nullable|date',
                    'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:4096',
                ]);
                $event->title = $data['title'];
                $event->description = $data['description'] ?? '';
                $event->starts_at = $data['starts_at'];
                $event->ends_at = $data['ends_at'] ?? null;
                $event->approval_status = 'pending';
                if ($request->hasFile('image')) {
                    if ($event->image) {
                        Storage::disk('public')->delete($event->image);
                    }
                    $event->image = $request->file('image')->store('eventos', 'public');
                }
                $event->save();
                return response()->json(['event' => $event, 'message' => 'Evento actualizado exitosamente']);
            });

            // Eliminar evento
            Route::delete('/events/{id}', function (Request $request, $id) {
                $event = PlaceEvent::findOrFail($id);
                // Solo el operador dueño o admin puede eliminar
                $user = $request->user();
                if ($user->role !== 'admin' && $event->place->user_id !== $user->id) {
                    return response()->json(['message' => 'No autorizado'], 403);
                }
                if ($event->image) {
                    Storage::disk('public')->delete($event->image);
                }
                $event->delete();
                return response()->json(['message' => 'Evento eliminado exitosamente']);
            });

                // Crear evento (operador/admin)
                Route::post('/places/{id}/events', function (Request $request, $id) {
                    $user = $request->user();
                    $place = TuristicPlace::findOrFail($id);
                    if (!in_array($user->role, ['operator', 'admin']) || ($user->role === 'operator' && $place->user_id !== $user->id)) {
                        return response()->json(['message' => 'No autorizado'], 403);
                    }
                    $data = $request->validate([
                        'title' => 'required|string|max:255',
                        'description' => 'nullable|string|max:1000',
                        'starts_at' => 'required|date',
                        'ends_at' => 'nullable|date',
                        'image' => 'required|image|mimes:jpg,jpeg,png,gif,svg,webp|max:4096',
                    ]);
                    $event = new PlaceEvent();
                    $event->place_id = $place->id;
                    $event->title = $data['title'];
                    $event->description = $data['description'] ?? '';
                    $event->starts_at = $data['starts_at'];
                    $event->ends_at = $data['ends_at'] ?? null;
                    $event->approval_status = 'pending';
                    $event->image = $request->file('image')->store('eventos', 'public');
                    $event->save();
                    return response()->json(['event' => $event, 'message' => 'Evento creado exitosamente']);
                });

        // ============ TURISTIC PLACES - CREATE/UPDATE/DELETE ============
        Route::post('/places', [TuristicPlaceApiController::class, 'store']);
        Route::put('/places/{id}', [TuristicPlaceApiController::class, 'update']);
        Route::delete('/places/{id}', [TuristicPlaceApiController::class, 'destroy']);
        Route::get('/user-places', [TuristicPlaceApiController::class, 'userPlaces']);

        // ============ REVIEWS ============
        Route::post('/places/{id}/reviews', [ReviewApiController::class, 'store']);
        Route::put('/reviews/{id}', [ReviewApiController::class, 'update']);
        Route::delete('/reviews/{id}', [ReviewApiController::class, 'destroy']);
        Route::post('/reviews/{id}/react', [ReviewApiController::class, 'react']);

        // ============ COMMENTS (sin calificación) ============
        Route::post('/places/{id}/comments', [CommentController::class, 'store']);
        Route::put('/comments/{id}', [CommentController::class, 'update']);
        Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

        // ============ OPERATOR REVIEW MODERATION ============
        Route::middleware('role:operator')->prefix('operator')->group(function () {
            Route::post('/reviews/{id}/restrict', function (Request $request, $id) {
                $review = reviews::with('place')->findOrFail($id);
                if (! $review->place || $review->place->user_id !== $request->user()->id) {
                    return response()->json(['message' => 'No autorizado'], 403);
                }

                $review->update([
                    'is_restricted' => true,
                    'restricted_by_role' => 'operator',
                    'restriction_reason' => null,
                ]);

                return response()->json([
                    'message' => 'Reseña restringida exitosamente',
                    'review' => $review,
                ]);
            });

            Route::post('/reviews/{id}/unrestrict', function (Request $request, $id) {
                $review = reviews::with('place')->findOrFail($id);
                if (! $review->place || $review->place->user_id !== $request->user()->id) {
                    return response()->json(['message' => 'No autorizado'], 403);
                }

                $review->update([
                    'is_restricted' => false,
                    'restricted_by_role' => null,
                    'restriction_reason' => null,
                ]);

                return response()->json([
                    'message' => 'Reseña desrestringida exitosamente',
                    'review' => $review,
                ]);
            });
                // Obtener todas las reseñas de los sitios del operador
                Route::get('/reviews', function (Request $request) {
                    $user = $request->user();
                    $places = TuristicPlace::where('user_id', $user->id)->pluck('id');
                    $reviews = reviews::with(['user:id,name', 'place:id,name'])
                        ->whereIn('place_id', $places)
                        ->orderBy('created_at', 'desc')
                        ->get();
                    return response()->json($reviews);
                });
                // Estadísticas del operador
                Route::get('/stats', function (Request $request) {
                    $user = $request->user();
                    // Sitios turísticos creados por el operador
                    $places = TuristicPlace::where('user_id', $user->id)->get();
                    $places_count = $places->count();
                    // Eventos creados por el operador
                    $events_count = PlaceEvent::whereIn('place_id', $places->pluck('id'))->count();
                    // Reseñas recibidas en sitios del operador
                    $reviews = reviews::whereIn('place_id', $places->pluck('id'))->get();
                    $reviews_count = $reviews->count();
                    // Turistas únicos que han visitado sitios del operador
                    $unique_turists = DB::table('user_place_visits')
                        ->join('turistic_places', 'user_place_visits.place_id', '=', 'turistic_places.id')
                        ->where('turistic_places.user_id', $user->id)
                        ->distinct('user_place_visits.user_id')
                        ->count('user_place_visits.user_id');

                    // Visitas totales a sitios del operador
                    $visits = DB::table('user_place_visits')
                        ->join('turistic_places', 'user_place_visits.place_id', '=', 'turistic_places.id')
                        ->where('turistic_places.user_id', $user->id)
                        ->count();

                    // Favoritos totales de sitios del operador
                    $favorites = DB::table('favorite_places')
                        ->join('turistic_places', 'favorite_places.place_id', '=', 'turistic_places.id')
                        ->where('turistic_places.user_id', $user->id)
                        ->count();

                    // Promedio de calificación de sitios del operador
                    $avg_rating = $reviews_count > 0 ? round($reviews->avg('rating'), 2) : 0.0;

                    // Comentarios recientes en sitios del operador (últimos 6)
                    $recent_comments = reviews::whereIn('place_id', $places->pluck('id'))
                        ->orderBy('created_at', 'desc')
                        ->limit(6)
                        ->get()
                        ->map(function ($review) {
                            return [
                                'place_name' => optional($review->place)->name,
                                'user_name' => optional($review->user)->name,
                                'created_at' => $review->created_at,
                                'comment' => $review->comment,
                            ];
                        });

                    return response()->json([
                        'places_count' => $places_count,
                        'events_count' => $events_count,
                        'reviews_count' => $reviews_count,
                        'unique_turists' => $unique_turists,
                        'visits' => $visits,
                        'favorites' => $favorites,
                        'avg_rating' => $avg_rating,
                        'recent_comments' => $recent_comments,
                    ]);
                });
        });

        // ============ ADMIN ROUTES ============
        Route::middleware('role:admin')->prefix('admin')->group(function () {
            // Listar todos los eventos turísticos
            Route::get('/events', function () {
                return \App\Models\PlaceEvent::orderBy('starts_at', 'desc')->get();
            });


            // Aprobar evento
            Route::post('/events/{id}/approve', function ($id) {
                $event = \App\Models\PlaceEvent::findOrFail($id);
                $wasPending = $event->approval_status === 'pending';
                $event->approval_status = 'approved';
                $event->save();

                // Solo notificar si estaba pendiente antes
                if ($wasPending) {
                    $place = $event->place;
                    // Obtener usuarios que tienen el sitio en favoritos
                    $users = $place->favoriteby ? $place->favoriteby : (method_exists($place, 'favoriteby') ? $place->favoriteby() : []);
                    if (is_callable($users)) $users = $users()->get();
                    foreach ($users as $user) {
                        // Notificación en la web
                        \App\Models\UserNotification::create([
                            'user_id' => $user->id,
                            'type' => 'event',
                            'title' => 'Nuevo evento en tu sitio favorito',
                            'message' => 'Se ha publicado el evento "' . $event->title . '" en ' . $place->name . '.',
                            'preview' => mb_substr($event->description ?? '', 0, 120),
                            'target_type' => 'event',
                            'target_id' => $event->id,
                            'place_id' => $place->id,
                            'place_name' => $place->name,
                        ]);
                        // Correo
                        try {
                            \Mail::to($user->email)->send(new \App\Mail\NewEventNotification($event, $place, $user));
                        } catch (\Exception $e) {
                            \Log::error('Error enviando correo de nuevo evento: ' . $e->getMessage());
                        }
                    }
                }
                return response()->json(['event' => $event, 'message' => 'Evento aprobado exitosamente']);
            });

            // Rechazar evento
            Route::post('/events/{id}/reject', function ($id) {
                $event = \App\Models\PlaceEvent::findOrFail($id);
                $event->approval_status = 'rejected';
                $event->save();
                return response()->json(['event' => $event, 'message' => 'Evento rechazado']);
            });

            // Eliminar evento (admin)
            Route::delete('/events/{id}', function ($id) {
                $event = \App\Models\PlaceEvent::findOrFail($id);
                if ($event->image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($event->image);
                }
                $event->delete();
                return response()->json(['message' => 'Evento eliminado exitosamente']);
            });
                        // Actualizar estado y aprobación de sitio turístico
                        Route::put('/places/{id}', function (Request $request, $id) {
                            $place = \App\Models\TuristicPlace::findOrFail($id);
                            $data = $request->validate([
                                'approval_status' => 'sometimes|in:pending,approved,rejected',
                                'archived_at' => 'nullable|date',
                                'opening_status' => 'sometimes|in:open,closed_temporarily,open_with_restrictions',
                            ]);
                            if (array_key_exists('approval_status', $data)) {
                                $place->approval_status = $data['approval_status'];
                            }
                            if (array_key_exists('archived_at', $data)) {
                                $place->archived_at = $data['archived_at'];
                            }
                            if (array_key_exists('opening_status', $data)) {
                                $place->opening_status = $data['opening_status'];
                            }
                            $place->save();
                            return response()->json(['place' => $place, 'message' => 'Sitio actualizado exitosamente', 'opening_status' => $place->opening_status]);
                        });
            // Dashboard con estadísticas para frontend
            Route::get('/dashboard', function () {
                $active_turistas = User::where('role', 'user')->where('status', 'active')->count();
                $active_operators = User::where('role', 'operator')->where('status', 'active')->count();
                $active_places = DB::table('turistic_places')->where('approval_status', 'approved')->count();
                $active_events = \App\Models\PlaceEvent::where('starts_at', '>=', now())
                    ->where('approval_status', 'approved')
                    ->whereNull('archived_at')
                    ->count();
                // Eliminar eventos archivados
                \App\Models\PlaceEvent::whereNotNull('archived_at')->delete();

                return response()->json([
                    'active_turistas' => $active_turistas,
                    'active_operators' => $active_operators,
                    'active_places' => $active_places,
                    'active_events' => $active_events,
                ]);
            });

            // Listar todos los usuarios
            Route::get('/users', function (Request $request) {
                $query = User::query();
                
                // Filtros opcionales
                if ($request->has('role')) {
                    $query->where('role', $request->role);
                }
                if ($request->has('status')) {
                    $query->where('status', $request->status);
                }
                
                $users = $query->orderBy('created_at', 'desc')->get();
                return response()->json($users);
            });

            // Crear operador (admin crea credenciales)
            Route::post('/users', function (Request $request) {
                $data = $request->validate([
                    'name' => 'required|string|max:255',
                    'last_name' => 'nullable|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|string|min:8',
                    'role' => 'required|in:user,operator,admin',
                    'country' => 'nullable|string|max:255',
                    'birth_date' => 'nullable|date',
                ]);

                $user = User::create([
                    'name' => $data['name'],
                    'last_name' => $data['last_name'] ?? null,
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role' => $data['role'],
                    'Country' => $data['country'] ?? null,
                    'date_of_birth' => $data['birth_date'] ?? null,
                    'status' => $data['role'] === 'operator' ? 'approved' : 'active',
                    'email_verified_at' => now(), // Admin-created accounts are pre-verified
                ]);

                return response()->json([
                    'user' => $user,
                    'message' => 'Usuario creado exitosamente',
                ], 201);
            });

            // Obtener un usuario específico
            Route::get('/users/{id}', function ($id) {
                $user = User::findOrFail($id);
                return response()->json($user);
            });

            // Actualizar usuario (cambiar rol, status, soft delete/reactivar)
            Route::put('/users/{id}', function (Request $request, $id) {
                $user = User::findOrFail($id);
                $data = $request->validate([
                    'name' => 'sometimes|string|max:255',
                    'last_name' => 'sometimes|string|max:255',
                    // No permitir cambio de email por API admin
                    'role' => 'sometimes|in:user,operator,admin',
                    'status' => 'sometimes|in:pending,approved,rejected,active,inactive',
                    'country' => 'nullable|string|max:255',
                    'birth_date' => 'nullable|date',
                ]);

                // Solo admin puede activar/desactivar usuarios
                if (!auth()->user() || auth()->user()->role !== 'admin') {
                    return response()->json(['error' => 'Solo el administrador puede realizar esta acción'], 403);
                }

                // Desactivar usuario (solo cambia status)
                if (array_key_exists('status', $data) && $data['status'] === 'inactive') {
                    $user->status = 'inactive';
                    $user->save();
                    return response()->json(['user' => $user, 'message' => 'Usuario desactivado (archivado)']);
                }

                // Reactivar usuario (solo cambia status)
                if (array_key_exists('status', $data) && $data['status'] === 'active') {
                    $user->status = 'active';
                    $user->save();
                    return response()->json(['user' => $user, 'message' => 'Usuario reactivado']);
                }

                // Cambio de rol u otros campos (excepto email)
                $user->update($data);
                return response()->json(['user' => $user, 'message' => 'Usuario actualizado exitosamente']);
            });

            // Eliminar usuario
            Route::delete('/users/{id}', function ($id) {
                $user = User::findOrFail($id);
                
                // Prevenir que el admin se elimine a sí mismo
                if ($user->id === auth()->id()) {
                    return response()->json([
                        'message' => 'No puedes eliminar tu propia cuenta',
                    ], 403);
                }

                $user->delete();

                return response()->json([
                    'message' => 'Usuario eliminado exitosamente',
                ]);
            });

            // Operadores pendientes de aprobación
            Route::get('/operators/pending', function () {
                $pendingOperators = User::where('role', 'operator')
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                return response()->json($pendingOperators);
            });

            // Aprobar/rechazar operador
            Route::post('/operators/{id}/approve', function ($id) {
                $operator = User::findOrFail($id);
                
                if ($operator->role !== 'operator') {
                    return response()->json(['message' => 'Este usuario no es un operador'], 400);
                }

                $operator->update(['status' => 'approved']);

                return response()->json([
                    'user' => $operator,
                    'message' => 'Operador aprobado exitosamente',
                ]);
            });

            Route::post('/operators/{id}/reject', function ($id) {
                $operator = User::findOrFail($id);
                
                if ($operator->role !== 'operator') {
                    return response()->json(['message' => 'Este usuario no es un operador'], 400);
                }

                $operator->update(['status' => 'rejected']);

                return response()->json([
                    'user' => $operator,
                    'message' => 'Operador rechazado',
                ]);
            });

            // Gestión de sitios turísticos (todos los sitios)
            Route::get('/places', [TuristicPlaceApiController::class, 'index']);
            Route::delete('/places/{id}', [TuristicPlaceApiController::class, 'destroy']);

            // Gestión de reseñas (admin)
            Route::get('/reviews', function () {
                return \App\Models\reviews::with([
                        'user:id,name',
                        'place:id,name'
                    ])
                    ->orderBy('created_at', 'desc')
                    ->get();
            });

            // Restringir reseña (admin)
            Route::post('/reviews/{id}/restrict', function ($id) {
                $review = \App\Models\reviews::findOrFail($id);
                $review->update([
                    'is_restricted' => true,
                    'restricted_by_role' => 'admin',
                    'restriction_reason' => null,
                ]);
                
                return response()->json([
                    'message' => 'Reseña restringida exitosamente',
                    'review' => $review,
                ]);
            });

            // Desrestringir reseña (admin)
            Route::post('/reviews/{id}/unrestrict', function ($id) {
                $review = \App\Models\reviews::findOrFail($id);
                $review->update([
                    'is_restricted' => false,
                    'restricted_by_role' => null,
                    'restriction_reason' => null,
                ]);
                
                return response()->json([
                    'message' => 'Reseña desrestringida exitosamente',
                    'review' => $review,
                ]);
            });
        });

        // ============ ADMIN: ETIQUETAS ============
        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::get('/preferences', function () {
                return \App\Models\preference::orderBy('name')->get();
            });

            Route::post('/preferences', function (Request $request) {
                $data = $request->validate([
                    'name' => 'required|string|max:255',
                    'image' => 'nullable|string|max:255',
                    'color' => 'required|string|max:20',
                ]);

                if (empty($data['image'])) {
                    $data['image'] = Str::slug($data['name'], '_');
                }

                $pref = \App\Models\preference::create($data);

                return response()->json(['preference' => $pref, 'message' => 'Etiqueta creada']);
            });

            Route::put('/preferences/{id}', function (Request $request, $id) {
                $pref = \App\Models\preference::findOrFail($id);
                $data = $request->validate([
                    'name' => 'required|string|max:255',
                    'image' => 'nullable|string|max:255',
                    'color' => 'required|string|max:20',
                ]);

                if (empty($data['image'])) {
                    $data['image'] = Str::slug($data['name'], '_');
                }

                $pref->update($data);

                return response()->json(['preference' => $pref, 'message' => 'Etiqueta actualizada']);
            });

            Route::delete('/preferences/{id}', function ($id) {
                $pref = \App\Models\preference::findOrFail($id);
                $pref->delete();

                return response()->json(['message' => 'Etiqueta eliminada']);
            });
        });
    });

// Verificar email (enlace firmado) y redirigir al frontend
Route::middleware('web')->get('/email/verify/{id}/{hash}', function (Request $request) {
    $user = User::findOrFail($request->route('id'));

    if (! hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
        abort(403);
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    $frontend = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173'));
    return redirect()->away(rtrim($frontend, '/') . '/email-verified?verified=1');
})->name('api.verification.verify')->middleware('throttle:6,1');


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/places/{id}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
});
