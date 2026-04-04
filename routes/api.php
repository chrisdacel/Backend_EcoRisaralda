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

        // Gestión de sitios turísticos (todos los sitios)
            Route::get('/places', [TuristicPlaceApiController::class, 'index']);

                    // ============ REVIEWS ============
        Route::post('/places/{id}/reviews', [ReviewApiController::class, 'store']);
        Route::put('/reviews/{id}', [ReviewApiController::class, 'update']);
        Route::delete('/reviews/{id}', [ReviewApiController::class, 'destroy']);
        Route::post('/reviews/{id}/react', [ReviewApiController::class, 'react']);