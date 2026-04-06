<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\preferenceController;
use App\Http\Controllers\TuristicPlaceController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\AdminFunctionController;

use Illuminate\Support\Facades\Route;
//Laravel default web routes file
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


//preferences routes


Route::get('/preferencias', [preferenceController::class, 'mostrardatosdepreferencias'])
    ->middleware(['auth', 'verified'])
    ->name('preferencias');
Route::post('/preferencias', [preferenceController::class, 'validarpreferencias'])
    ->middleware(['auth', 'verified'])
    ->name('preferencias');

//crear sitio ecoturistico
Route::get('/Crear_sitio', [TuristicPlaceController::class, 'crear'])
                            //con esto le pongo un nombre a la ruta para no tener
    ->middleware(['auth', 'role:operator,admin'])
    ->name('crear_sitio');

Route::post('/Crear_sitio', [TuristicPlaceController::class, 'validarsitio'])
    ->middleware(['auth', 'role:operator,admin'])
    ->name('guardar_sitio');

//gestion y eliminar  sitios de operador y admin
Route::get('/Gestion_sitio', [TuristicPlaceController::class, 'gestionsitios'])
    ->middleware(['auth', 'role:operator,admin'])
    ->name('gestionar_sitios');
Route::delete('/Gestion_sitio/{id}', [TuristicPlaceController::class, 'destroy'])
    ->middleware(['auth', 'role:operator,admin'])
    ->name('eliminar_sitio');
Route::patch('/Gestion_sitio/{id}/opening_status', [TuristicPlaceController::class, 'toggleOpeningStatus'])
    ->middleware(['auth', 'role:operator,admin'])
    ->name('toggle_opening_status');
//editar sitios de oprador y admin
Route::get('/Editar_sitio/{id}', [TuristicPlaceController::class, 'editar'])
    ->middleware(['auth', 'role:operator,admin'])
    ->name('Modificar_sitio');
Route::put('/Editar_sitio/{id}', [TuristicPlaceController::class, 'sitioactualizado'])
    ->middleware(['auth', 'role:operator,admin'])
    ->name('sitio_actualizado');

//visualizar sitio ecoturistico
Route::get('/Sitio/{id}', [TuristicPlaceController::class, 'ver'])->name('sitio_ecoturistico');
//publicar comentario o reseña
Route::post('/Sitio/{id}', [ReviewsController::class, 'publicarreseña'])->name('sitio_ecoturistico');

//eliminar reseña
Route::delete('/Sitio/{id}', [ReviewsController::class, 'eliminarreseña'])->name('eliminar_reseña');

//añadir a favoritos
Route::post('/Sitio/{id}/favorite', [TuristicPlaceController::class, 'favoritos'])->name('agregar_favorito');
//eliminar de favoritos
Route::delete('/Sitio/{id}/favorite', [TuristicPlaceController::class, 'removeFavorite'])->name('eliminar_favorito');

Route::get('/Sitios_favoritos',[TuristicPlaceController::class,'versitiosfavoritos'])
    ->middleware(['auth', 'verified'])
    ->name('sitios_favoritos');

//ver coleccion de todos los sitios 
Route::get('/Coleccion', [TuristicPlaceController::class, 'coleccion'])
    ->name('coleccion_sitios');

//panel de control admin
Route::get('/panel_control', [AdminFunctionController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('Modificar_sitio');

require __DIR__.'/auth.php';
