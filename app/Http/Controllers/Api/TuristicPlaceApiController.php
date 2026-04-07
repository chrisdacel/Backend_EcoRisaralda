<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TuristicPlace;
use App\Models\reviews;
use App\Models\PlaceEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class TuristicPlaceApiController extends Controller
{
    /**
     * GET /api/places
     * List all turistic places with optional search
     */
    public function index(Request $request)
    {
        $query = TuristicPlace::with(['user', 'label'])
            ->whereNull('archived_at')
            ->latest();

        $user = $request->user();
        $isAdmin = $user && $user->role === 'admin';
        $isOperator = $user && $user->role === 'operator';
        if ($isAdmin) {
            // Admin ve todos los sitios
        } elseif ($isOperator) {
            $query->where(function ($subQuery) use ($user) {
                $subQuery->where('approval_status', 'approved')
                    ->orWhere('user_id', $user->id);
            });
        } else {
            $query->where('approval_status', 'approved');
        }
        
        // Si hay un parámetro de búsqueda, filtrar resultados por nombre
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        return response()->json($query->get());
    }

    /**
     * GET /api/places/{id}
     * Show single place with reviews
     */
    public function show(Request $request, $id)
    {
        $place = TuristicPlace::with(['user', 'label'])
            ->whereNull('archived_at')
            ->where('id', $id)
            ->first();

        if (! $place) {
            return response()->json(['message' => 'Sitio no encontrado'], 404);
        }

        $canViewUnapproved = $request->user()
            && ($request->user()->role === 'admin' || $request->user()->id === $place->user_id);
        if (! $canViewUnapproved && $place->approval_status !== 'approved') {
            return response()->json(['message' => 'Sitio no disponible'], 404);
        }
        $expiredEvents = $place->events()
            ->whereNull('archived_at')
            ->where('starts_at', '<', now())
            ->get();
        foreach ($expiredEvents as $expiredEvent) {
            if ($expiredEvent->image) {
                Storage::disk('public')->delete($expiredEvent->image);
            }
            $expiredEvent->delete();
        }
        $eventQuery = $place->events()->whereNull('archived_at');
        if (! $canViewUnapproved) {
            $eventQuery->where('approval_status', 'approved');
        }
        $event = $eventQuery
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at', 'asc')
            ->first();
        $reviews = reviews::where('place_id', $id)
            ->with(['user', 'reactions'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular promedio solo con ratings válidos (no null)
        $ratings = $reviews->pluck('rating')->filter();
        $average = $ratings->count() > 0 ? round($ratings->avg(), 2) : null;

        // Procesar cada review para agregar contadores y reacción del usuario
        $userId = $request->user() ? $request->user()->id : null;
        $reviews->each(function ($review) use ($userId) {
            $review->likes_count = $review->reactions->where('type', 'like')->count();
            $review->dislikes_count = $review->reactions->where('type', 'dislike')->count();
            if ($userId) {
                $userReaction = $review->reactions->first(function ($reaction) use ($userId) {
                    return $reaction->user_id === $userId;
                });
                $review->user_reaction = $userReaction ? $userReaction->type : null;
            } else {
                $review->user_reaction = null;
            }
            unset($review->reactions);
        });

        return response()->json([
            'place' => $place,
            'reviews' => $reviews,
            'average_rating' => $average,
            'event' => $event,
        ]);
    }

    /**
     * POST /api/places
     * Create a new turistic place (operator/admin only)
     */
    public function store(Request $request)
    {
        // Authorization check
        if (!in_array($request->user()->role, ['operator', 'admin'])) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Validation
        $validated = $request->validate([
            'nombre' => 'required|string|min:5|max:80',
            'slogan' => 'required|string|min:5|max:120',
            'descripcion' => 'required|string|min:30|max:1000',
            'localizacion' => 'required|string|min:10|max:500',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'clima' => 'required|string|min:20|max:600',
            'caracteristicas' => 'required|string|min:20|max:600',
            'flora' => 'required|string|min:20|max:600',
            'infraestructura' => 'required|string|min:20|max:600',
            'recomendacion' => 'required|string|min:20|max:600',
            'preferences' => 'required|array|min:1',
            'preferences.*' => 'integer|exists:preferences,id',
            'contacto' => 'nullable|string|max:200',
            'dias_abiertos' => 'nullable',
            'estado_apertura' => 'nullable|in:open,closed_temporarily,open_with_restrictions',
            'portada' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'clima_img' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'caracteristicas_img' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'flora_img' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'infraestructura_img' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'nombre.required' => 'El nombre del sitio es obligatorio.',
            'nombre.min' => 'El nombre del sitio debe tener al menos 5 caracteres.',
            'nombre.max' => 'El nombre del sitio no debe tener más de 80 caracteres.',
            'slogan.required' => 'El slogan es obligatorio.',
            'slogan.min' => 'El slogan debe tener al menos 5 caracteres.',
            'slogan.max' => 'El slogan no debe tener más de 120 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.min' => 'La descripción debe tener al menos 30 caracteres.',
            'descripcion.max' => 'La descripción no debe tener más de 1000 caracteres.',
            'localizacion.required' => 'La localización es obligatoria.',
            'localizacion.min' => 'La localización debe tener al menos 10 caracteres.',
            'localizacion.max' => 'La localización no debe tener más de 500 caracteres.',
            'lat.required' => 'La latitud es obligatoria. Por favor, selecciona una ubicación en el mapa.',
            'lat.numeric' => 'La latitud debe ser un número válido.',
            'lng.required' => 'La longitud es obligatoria. Por favor, selecciona una ubicación en el mapa.',
            'lng.numeric' => 'La longitud debe ser un número válido.',
            'clima.required' => 'La descripción del clima es obligatoria.',
            'clima.min' => 'La descripción del clima debe tener al menos 20 caracteres.',
            'clima.max' => 'La descripción del clima no debe tener más de 600 caracteres.',
            'caracteristicas.required' => 'Las características son obligatorias.',
            'caracteristicas.min' => 'Las características deben tener al menos 20 caracteres.',
            'caracteristicas.max' => 'Las características no deben tener más de 600 caracteres.',
            'flora.required' => 'La descripción de flora y fauna es obligatoria.',
            'flora.min' => 'La descripción de flora y fauna debe tener al menos 20 caracteres.',
            'flora.max' => 'La descripción de flora y fauna no debe tener más de 600 caracteres.',
            'infraestructura.required' => 'La descripción de infraestructura es obligatoria.',
            'infraestructura.min' => 'La descripción de infraestructura debe tener al menos 20 caracteres.',
            'infraestructura.max' => 'La descripción de infraestructura no debe tener más de 600 caracteres.',
            'recomendacion.required' => 'Las recomendaciones son obligatorias.',
            'recomendacion.min' => 'Las recomendaciones deben tener al menos 20 caracteres.',
            'recomendacion.max' => 'Las recomendaciones no deben tener más de 600 caracteres.',
            'portada.required' => 'La imagen de portada es obligatoria.',
            'portada.image' => 'El archivo de portada debe ser una imagen.',
            'portada.mimes' => 'La imagen de portada debe ser de tipo: jpg, jpeg, png o webp.',
            'portada.max' => 'La imagen de portada no debe pesar más de 5MB.',
            'clima_img.required' => 'La imagen del clima es obligatoria.',
            'clima_img.image' => 'El archivo del clima debe ser una imagen.',
            'clima_img.mimes' => 'La imagen del clima debe ser de tipo: jpg, jpeg, png o webp.',
            'clima_img.max' => 'La imagen del clima no debe pesar más de 5MB.',
            'caracteristicas_img.required' => 'La imagen de características es obligatoria.',
            'caracteristicas_img.image' => 'El archivo de características debe ser una imagen.',
            'caracteristicas_img.mimes' => 'La imagen de características debe ser de tipo: jpg, jpeg, png o webp.',
            'caracteristicas_img.max' => 'La imagen de características no debe pesar más de 5MB.',
            'flora_img.required' => 'La imagen de flora y fauna es obligatoria.',
            'flora_img.image' => 'El archivo de flora y fauna debe ser una imagen.',
            'flora_img.mimes' => 'La imagen de flora y fauna debe ser de tipo: jpg, jpeg, png o webp.',
            'flora_img.max' => 'La imagen de flora y fauna no debe pesar más de 5MB.',
            'infraestructura_img.required' => 'La imagen de infraestructura es obligatoria.',
            'infraestructura_img.image' => 'El archivo de infraestructura debe ser una imagen.',
            'infraestructura_img.mimes' => 'La imagen de infraestructura debe ser de tipo: jpg, jpeg, png o webp.',
            'infraestructura_img.max' => 'La imagen de infraestructura no debe pesar más de 5MB.',
        ]);

        $openDays = null;
        $openDaysRaw = $request->input('dias_abiertos');
        if (is_string($openDaysRaw)) {
            $decoded = json_decode($openDaysRaw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $openDays = $decoded;
            }
        } elseif (is_array($openDaysRaw)) {
            $openDays = $openDaysRaw;
        }

        // Store images
        $portada_path = $this->processAndStoreImage($request->file('portada'), 'portadas');
        $clima_path = $this->processAndStoreImage($request->file('clima_img'), 'clima');
        $caracteristicas_path = $this->processAndStoreImage($request->file('caracteristicas_img'), 'caracteristicas');
        $flora_path = $this->processAndStoreImage($request->file('flora_img'), 'flora');
        $infraestructura_path = $this->processAndStoreImage($request->file('infraestructura_img'), 'infraestructura');

        // Create place
        $place = TuristicPlace::create([
            'user_id' => $request->user()->id,
            'name' => $validated['nombre'],
            'slogan' => $validated['slogan'],
            'description' => $validated['descripcion'],
            'localization' => $validated['localizacion'],
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'Weather' => $validated['clima'],
            'features' => $validated['caracteristicas'],
            'flora' => $validated['flora'],
            'estructure' => $validated['infraestructura'],
            'tips' => $validated['recomendacion'],
            'contact_info' => $validated['contacto'] ?? null,
            'open_days' => $openDays,
            'opening_status' => $validated['estado_apertura'] ?? 'open',
            'cover' => $portada_path,
            'Weather_img' => $clima_path,
            'features_img' => $caracteristicas_path,
            'flora_img' => $flora_path,
            'estructure_img' => $infraestructura_path,
            'terminos' => true,
            'politicas' => true,
            'approval_status' => 'pending',
        ]);

        $place->label()->attach($validated['preferences']);

        return response()->json([
            'message' => 'Sitio creado exitosamente',
            'place' => $place,
        ], 201);
    }

    /**
     * PUT /api/places/{id}
     * Update a turistic place (operator/admin only)
     */
    public function update(Request $request, $id)
    {
        $place = TuristicPlace::whereNull('archived_at')
            ->where('id', $id)
            ->firstOrFail();

        // Authorization check
        if ($request->user()->id !== $place->user_id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Validation
        // Permitir actualización parcial solo del estado
        if ($request->has('opening_status') && count($request->all()) === 1) {
            $validated = $request->validate([
                'opening_status' => 'required|in:open,closed_temporarily,open_with_restrictions',
            ]);
            $place = TuristicPlace::findOrFail($id);
            $place->opening_status = $validated['opening_status'];
            $place->save();
            return response()->json([
                'message' => 'Estado actualizado exitosamente',
                'place' => $place,
                'opening_status' => $place->opening_status,
            ]);
        }
        // Actualización completa
        $validated = $request->validate([
            'nombre' => 'required|string|min:5|max:80',
            'slogan' => 'required|string|min:5|max:120',
            'descripcion' => 'required|string|min:30|max:1000',
            'localizacion' => 'required|string|min:10|max:500',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'clima' => 'required|string|min:20|max:600',
            'caracteristicas' => 'required|string|min:20|max:600',
            'flora' => 'required|string|min:20|max:600',
            'infraestructura' => 'required|string|min:20|max:600',
            'recomendacion' => 'required|string|min:20|max:600',
            'preferences' => 'required|array|min:1',
            'preferences.*' => 'integer|exists:preferences,id',
            'contacto' => 'nullable|string|max:200',
            'dias_abiertos' => 'nullable',
            'estado_apertura' => 'nullable|in:open,closed_temporarily,open_with_restrictions',
            'opening_status' => 'nullable|in:open,closed_temporarily,open_with_restrictions',
            'portada' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'clima_img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'caracteristicas_img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'flora_img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'infraestructura_img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'nombre.required' => 'El nombre del sitio es obligatorio.',
            'nombre.min' => 'El nombre del sitio debe tener al menos 5 caracteres.',
            'nombre.max' => 'El nombre del sitio no debe tener más de 80 caracteres.',
            'slogan.required' => 'El slogan es obligatorio.',
            'slogan.min' => 'El slogan debe tener al menos 5 caracteres.',
            'slogan.max' => 'El slogan no debe tener más de 120 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.min' => 'La descripción debe tener al menos 30 caracteres.',
            'descripcion.max' => 'La descripción no debe tener más de 1000 caracteres.',
            'localizacion.required' => 'La localización es obligatoria.',
            'localizacion.min' => 'La localización debe tener al menos 10 caracteres.',
            'localizacion.max' => 'La localización no debe tener más de 500 caracteres.',
            'lat.required' => 'La latitud es obligatoria. Por favor, selecciona una ubicación en el mapa.',
            'lat.numeric' => 'La latitud debe ser un número válido.',
            'lng.required' => 'La longitud es obligatoria. Por favor, selecciona una ubicación en el mapa.',
            'lng.numeric' => 'La longitud debe ser un número válido.',
            'clima.required' => 'La descripción del clima es obligatoria.',
            'clima.min' => 'La descripción del clima debe tener al menos 20 caracteres.',
            'clima.max' => 'La descripción del clima no debe tener más de 600 caracteres.',
            'caracteristicas.required' => 'Las características son obligatorias.',
            'caracteristicas.min' => 'Las características deben tener al menos 20 caracteres.',
            'caracteristicas.max' => 'Las características no deben tener más de 600 caracteres.',
            'flora.required' => 'La descripción de flora y fauna es obligatoria.',
            'flora.min' => 'La descripción de flora y fauna debe tener al menos 20 caracteres.',
            'flora.max' => 'La descripción de flora y fauna no debe tener más de 600 caracteres.',
            'infraestructura.required' => 'La descripción de infraestructura es obligatoria.',
            'infraestructura.min' => 'La descripción de infraestructura debe tener al menos 20 caracteres.',
            'infraestructura.max' => 'La descripción de infraestructura no debe tener más de 600 caracteres.',
            'recomendacion.required' => 'Las recomendaciones son obligatorias.',
            'recomendacion.min' => 'Las recomendaciones deben tener al menos 20 caracteres.',
            'recomendacion.max' => 'Las recomendaciones no deben tener más de 600 caracteres.',
            'portada.image' => 'El archivo de portada debe ser una imagen.',
            'portada.mimes' => 'La imagen de portada debe ser de tipo: jpg, jpeg, png o webp.',
            'portada.max' => 'La imagen de portada no debe pesar más de 5MB.',
            'clima_img.image' => 'El archivo del clima debe ser una imagen.',
            'clima_img.mimes' => 'La imagen del clima debe ser de tipo: jpg, jpeg, png o webp.',
            'clima_img.max' => 'La imagen del clima no debe pesar más de 5MB.',
            'caracteristicas_img.image' => 'El archivo de características debe ser una imagen.',
            'caracteristicas_img.mimes' => 'La imagen de características debe ser de tipo: jpg, jpeg, png o webp.',
            'caracteristicas_img.max' => 'La imagen de características no debe pesar más de 5MB.',
            'flora_img.image' => 'El archivo de flora y fauna debe ser una imagen.',
            'flora_img.mimes' => 'La imagen de flora y fauna debe ser de tipo: jpg, jpeg, png o webp.',
            'flora_img.max' => 'La imagen de flora y fauna no debe pesar más de 5MB.',
            'infraestructura_img.image' => 'El archivo de infraestructura debe ser una imagen.',
            'infraestructura_img.mimes' => 'La imagen de infraestructura debe ser de tipo: jpg, jpeg, png o webp.',
            'infraestructura_img.max' => 'La imagen de infraestructura no debe pesar más de 5MB.',
        ]);

        $openDays = null;
        $openDaysRaw = $request->input('dias_abiertos');
        if ($request->has('dias_abiertos')) {
            if (is_string($openDaysRaw)) {
                $decoded = json_decode($openDaysRaw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $openDays = $decoded;
                }
            } elseif (is_array($openDaysRaw)) {
                $openDays = $openDaysRaw;
            }
        }

        $preferencesChanged = false;
        $currentPreferences = $place->label()->pluck('preferences.id')->toArray();
        $incomingPreferences = $validated['preferences'] ?? [];
        sort($currentPreferences);
        sort($incomingPreferences);
        if ($currentPreferences !== $incomingPreferences) {
            $preferencesChanged = true;
        }

        $placeChanged = false;
        if ($place->name !== $validated['nombre']) $placeChanged = true;
        if ($place->slogan !== $validated['slogan']) $placeChanged = true;
        if ($place->description !== $validated['descripcion']) $placeChanged = true;
        if ($place->localization !== $validated['localizacion']) $placeChanged = true;
        if ((string) $place->lat !== (string) $validated['lat']) $placeChanged = true;
        if ((string) $place->lng !== (string) $validated['lng']) $placeChanged = true;
        if ($place->Weather !== $validated['clima']) $placeChanged = true;
        if ($place->features !== $validated['caracteristicas']) $placeChanged = true;
        if ($place->flora !== $validated['flora']) $placeChanged = true;
        if ($place->estructure !== $validated['infraestructura']) $placeChanged = true;
        if ($place->tips !== $validated['recomendacion']) $placeChanged = true;
        if (array_key_exists('contacto', $validated) && ($place->contact_info ?? '') !== ($validated['contacto'] ?? '')) {
            $placeChanged = true;
        }
        if ($request->has('dias_abiertos')) {
            $currentOpenDays = is_array($place->open_days) ? $place->open_days : [];
            $nextOpenDays = is_array($openDays) ? $openDays : [];
            if ($currentOpenDays != $nextOpenDays) {
                $placeChanged = true;
            }
        }
        if (array_key_exists('estado_apertura', $validated) && ($place->opening_status ?? '') !== ($validated['estado_apertura'] ?? '')) {
            $placeChanged = true;
        }
        if ($preferencesChanged) {
            $placeChanged = true;
        }

        // Update text fields
        $place->name = $validated['nombre'];
        $place->slogan = $validated['slogan'];
        $place->description = $validated['descripcion'];
        $place->localization = $validated['localizacion'];
        $place->lat = $validated['lat'];
        $place->lng = $validated['lng'];
        $place->Weather = $validated['clima'];
        $place->features = $validated['caracteristicas'];
        $place->flora = $validated['flora'];
        $place->estructure = $validated['infraestructura'];
        $place->tips = $validated['recomendacion'];
        if (array_key_exists('contacto', $validated)) {
            $place->contact_info = $validated['contacto'];
        }
        if ($request->has('dias_abiertos')) {
            $place->open_days = $openDays;
        }
        if (array_key_exists('opening_status', $validated)) {
            $place->opening_status = $validated['opening_status'];
        } else if (array_key_exists('estado_apertura', $validated)) {
            $place->opening_status = $validated['estado_apertura'];
        }

        // Update images if provided
        if ($request->hasFile('portada')) {
            if ($place->cover) Storage::disk('public')->delete($place->cover);
            $place->cover = $this->processAndStoreImage($request->file('portada'), 'portadas');
        }
        if ($request->hasFile('clima_img')) {
            if ($place->Weather_img) Storage::disk('public')->delete($place->Weather_img);
            $place->Weather_img = $this->processAndStoreImage($request->file('clima_img'), 'clima');
        }
        if ($request->hasFile('caracteristicas_img')) {
            if ($place->features_img) Storage::disk('public')->delete($place->features_img);
            $place->features_img = $this->processAndStoreImage($request->file('caracteristicas_img'), 'caracteristicas');
        }
        if ($request->hasFile('flora_img')) {
            if ($place->flora_img) Storage::disk('public')->delete($place->flora_img);
            $place->flora_img = $this->processAndStoreImage($request->file('flora_img'), 'flora');
        }
        if ($request->hasFile('infraestructura_img')) {
            if ($place->estructure_img) Storage::disk('public')->delete($place->estructure_img);
            $place->estructure_img = $this->processAndStoreImage($request->file('infraestructura_img'), 'infraestructura');
        }

        if ($request->hasFile('portada')
            || $request->hasFile('clima_img')
            || $request->hasFile('caracteristicas_img')
            || $request->hasFile('flora_img')
            || $request->hasFile('infraestructura_img')) {
            $placeChanged = true;
        }

        if ($placeChanged) {
            $place->approval_status = 'pending';
        }

        $place->save();

        $place->label()->sync($validated['preferences']);

        return response()->json([
            'message' => 'Sitio actualizado exitosamente',
            'place' => $place,
            'opening_status' => $place->opening_status,
        ]);
    }

    /**
     * DELETE /api/places/{id}
     * Delete a turistic place (operator/admin only)
     */
    public function destroy(Request $request, $id)
    {
        $place = TuristicPlace::whereNull('archived_at')
            ->where('id', $id)
            ->firstOrFail();

        // Authorization check
        if ($request->user()->id !== $place->user_id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Delete images
        if ($place->cover) Storage::disk('public')->delete($place->cover);
        if ($place->Weather_img) Storage::disk('public')->delete($place->Weather_img);
        if ($place->features_img) Storage::disk('public')->delete($place->features_img);
        if ($place->flora_img) Storage::disk('public')->delete($place->flora_img);
        if ($place->estructure_img) Storage::disk('public')->delete($place->estructure_img);

        $place->delete();

        return response()->json(['message' => 'Sitio eliminado exitosamente']);
    }

    /**
     * GET /api/places/{id}/user-places
     * Get all places created by authenticated user
     */
    public function userPlaces(Request $request)
    {
        $user = $request->user();
        
        if ($user->role === 'operator') {
            $places = TuristicPlace::with([
                    'user',
                    'events' => function ($query) {
                        $query->whereNull('archived_at')
                            ->orderBy('starts_at', 'desc');
                    },
                ])
                ->where('user_id', $user->id)
                ->whereNull('archived_at')
                ->get();
        } elseif ($user->role === 'admin') {
            $places = TuristicPlace::with([
                    'user',
                    'events' => function ($query) {
                        $query->whereNull('archived_at')
                            ->orderBy('starts_at', 'desc');
                    },
                ])
                ->whereNull('archived_at')
                ->get();
        } else {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return response()->json($places);
    }
    /**
     * Helper to process image with Intervention Image and store as WebP
     */
    private function processAndStoreImage($file, $pathPrefix)
    {
        if (!$file) return null;
        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        $image = $manager->read($file);
        $image->scaleDown(1920, 1920);
        $encoded = $image->toWebp(80);
        $filename = uniqid() . '.webp';
        $fullPath = $pathPrefix . '/' . $filename;
        \Illuminate\Support\Facades\Storage::disk('public')->put($fullPath, $encoded->toString());
        return $fullPath;
    }
}
