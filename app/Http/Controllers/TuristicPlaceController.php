<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TuristicPlace;
use App\Models\reviews;
use App\Models\rate;
use App\Models\FavoritePlace;
use App\Models\LabelPlace;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewPlaceNotification;
use App\Models\User;


class TuristicPlaceController extends Controller
{
    public function crear()
    {
          $preferences = \App\Models\preference::all();
     
        return view('sitios_ecoturisticos.Crear_sitio',compact('preferences'));
    }

    public function validarsitio(Request $request)
    {
        $request->validate([
            'nombre'               => 'required|string|max:255',
            'slogan'               => 'required|string|max:255',
            'descripcion'          => 'required|string|min:10',
            'localizacion'         => 'required|string|min:10',
            'lat'                  =>'required',
            'lng'                  => 'required',
            'clima'                => 'required|string|min:10',
            'caracteristicas'      => 'required|string|min:10',
            'flora'                => 'required|string|min:10',
            'infraestructura'      => 'required|string|min:10',
            'recomendacion'        => 'required|string|min:10',
             'preferences' => 'required|array|min:1',

            // imágenes
            'portada'              => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'clima_img'            => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'caracteristicas_img'  => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'flora_img'            => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'infraestructura_img'  => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',

            // términos y políticas
            'terminos'             => 'accepted',
            'politicas'            => 'accepted',

        ], [
            'nombre.required'   => 'Debe ingresar el nombre del sitio.',
            'slogan.required'   => 'Debe ingresar el slogan.',
            'descripcion.required' => 'Debe ingresar la descripción.',
            'localizacion.required' => 'Debe ingresar la localización.',
            'lat.required'          => 'debe ubicar la latitud',
            'lng.required'          =>'debe ubicar la longitud',
            'clima.required' => 'Debe ingresar el clima.',
            'caracteristicas.required' => 'Debe ingresar las características.',
            'flora.required' => 'Debe ingresar la flora y fauna.',
            'infraestructura.required' => 'Debe ingresar la infraestructura.',
            'recomendacion.required' => 'Debe ingresar recomendaciones.',

            'terminos.accepted' => 'Debe aceptar los términos.',
            'politicas.accepted' => 'Debe aceptar las políticas.',
            'preferences.required'=>'Debe aceptar al menos una etiqueta del sitio'
        ]);

        // Guardar imágenes
        $portada_path = $request->file('portada')->store('portadas', 'public');
        $clima_path = $request->file('clima_img')->store('clima', 'public');
        $caracteristicas_path = $request->file('caracteristicas_img')->store('caracteristicas', 'public');
        $flora_path = $request->file('flora_img')->store('flora', 'public');
        $infraestructura_path = $request->file('infraestructura_img')->store('infraestructura', 'public');

    $place = TuristicPlace::create([
        'user_id'             => auth()->id(),
        'name'                => $request->nombre,
        'slogan'              => $request->slogan,
        'description'         => $request->descripcion,
        'localization'        => $request->localizacion,
        'lat'                 => $request->lat,
        'lng'                 => $request->lng,
        'Weather'             => $request->clima,
        'features'            => $request->caracteristicas,
        'flora'               => $request->flora,
        'estructure'          => $request->infraestructura,
        'tips'                => $request->recomendacion,
        'cover'               => $portada_path,
        'Weather_img'         => $clima_path,
        'features_img'        => $caracteristicas_path,
        'flora_img'           => $flora_path,
        'estructure_img'      => $infraestructura_path,
        'terminos'            => true,
        'politicas'           => true,
    ]);
        $place->label()->attach($request->preferences);

        

        return redirect()->route('gestionar_sitios')->with('success', 'Sitio creado correctamente.');
    }
  public function gestionsitios()
{
    $user = auth()->user();
    
    if ($user->role == 'operator') {
        $places = TuristicPlace::where('user_id', $user->id)->get();
    } elseif ($user->role == 'admin') {
        $places = TuristicPlace::all();
    } else {
      
        abort(403, 'No tienes permisos para acceder a esta página');
    }
    
    return view('sitios_ecoturisticos.Gestion_sitio', compact('user', 'places'));
}

    public function destroy($id)
{
    $place = TuristicPlace::findOrFail($id);
    
    // Eliminar la imagen del storage
    if ($place->cover) {
        Storage::disk('public')->delete($place->cover);
    }
    
    $place->delete();
    
    return redirect()->route('gestionar_sitios')->with('success', 'Sitio eliminado correctamente');
}

    public function editar($id)
    {
        $place = TuristicPlace::findOrFail($id);
        return view('sitios_ecoturisticos.Editar_sitio', compact('place'));
    }
    public function sitioactualizado(Request $request, $id)
{
    $place = TuristicPlace::findOrFail($id);
    
    // Validación
    $request->validate([
        'nombre'               => 'required|string|max:255',
        'slogan'               => 'required|string|max:255',
        'descripcion'          => 'required|string|min:10',
        'localizacion'         => 'required|string|min:10',
        'lat'                  => 'required',
        'lng'                  => 'required',
        'clima'                => 'required|string|min:10',
        'caracteristicas'      => 'required|string|min:10',
        'flora'                => 'required|string|min:10',
        'infraestructura'      => 'required|string|min:10',
        'recomendacion'        => 'required|string|min:10',

        // Imágenes opcionales
        'portada'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        'clima_img'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        'caracteristicas_img'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        'flora_img'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        'infraestructura_img'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
    ], [
        'nombre.required'   => 'Debe ingresar el nombre del sitio.',
        'slogan.required'   => 'Debe ingresar el slogan.',
        'descripcion.required' => 'Debe ingresar la descripción.',
        'localizacion.required' => 'Debe ingresar la localización.',
        'lat.required'          => 'Debe ubicar la latitud',
        'lng.required'          => 'Debe ubicar la longitud',
        'clima.required' => 'Debe ingresar el clima.',
        'caracteristicas.required' => 'Debe ingresar las características.',
        'flora.required' => 'Debe ingresar la flora y fauna.',
        'infraestructura.required' => 'Debe ingresar la infraestructura.',
        'recomendacion.required' => 'Debe ingresar recomendaciones.',
    ]);

    // Actualizar campos de texto
    $place->name = $request->nombre;
    $place->slogan = $request->slogan;
    $place->description = $request->descripcion;
    $place->localization = $request->localizacion;
    $place->lat = $request->lat;
    $place->lng = $request->lng;
    $place->Weather = $request->clima;
    $place->features = $request->caracteristicas;
    $place->flora = $request->flora;
    $place->estructure = $request->infraestructura;
    $place->tips = $request->recomendacion;

    // Actualizar portada si se subió nueva imagen
    if ($request->hasFile('portada')) {
        // Eliminar imagen anterior
        if ($place->cover) {
            Storage::disk('public')->delete($place->cover);
        }
        // Guardar nueva imagen
        $place->cover = $request->file('portada')->store('portadas', 'public');
    }

    // Actualizar imagen de clima
    if ($request->hasFile('clima_img')) {
        if ($place->Weather_img) {
            Storage::disk('public')->delete($place->Weather_img);
        }
        $place->Weather_img = $request->file('clima_img')->store('clima', 'public');
    }

    // Actualizar imagen de características
    if ($request->hasFile('caracteristicas_img')) {
        if ($place->features_img) {
            Storage::disk('public')->delete($place->features_img);
        }
        $place->features_img = $request->file('caracteristicas_img')->store('caracteristicas', 'public');
    }

    // Actualizar imagen de flora
    if ($request->hasFile('flora_img')) {
        if ($place->flora_img) {
            Storage::disk('public')->delete($place->flora_img);
        }
        $place->flora_img = $request->file('flora_img')->store('flora', 'public');
    }

    // Actualizar imagen de infraestructura
    if ($request->hasFile('infraestructura_img')) {
        if ($place->estructure_img) {
            Storage::disk('public')->delete($place->estructure_img);
        }
        $place->estructure_img = $request->file('infraestructura_img')->store('infraestructura', 'public');
    }

    $place->save();

    return redirect()->route('gestionar_sitios')->with('success', 'Sitio actualizado correctamente');
}
    public function toggleOpeningStatus(Request $request, $id)
    {
        $place = TuristicPlace::findOrFail($id);
        $user = auth()->user();

        if ($user->role == 'operator' && $place->user_id != $user->id) {
            abort(403, 'No tienes permisos para cambiar el estado de este sitio');
        }

        if ($request->has('opening_status')) {
            $value = filter_var($request->input('opening_status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            $place->opening_status = is_null($value) ? ! $place->opening_status : $value;
        } else {
            $place->opening_status = ! $place->opening_status;
        }

        $place->save();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'opening_status' => (bool) $place->opening_status]);
        }

        return redirect()->back()->with('success', 'Estado de apertura actualizado.');
    }
    public function ver($id){

        $place = TuristicPlace::findOrFail($id);
         $user = auth()->user();
            $reviews = reviews::where('place_id', $id)
                     ->with('user') 
                     ->orderBy('created_at', 'desc')
                     ->get();
            $rate= rate::where('place_id',$id)->first();
        
        return view('sitios_ecoturisticos.Sitio', compact('user', 'place', 'reviews', 'rate'));
        
      

        // Enviar notificaciones por correo a usuarios cuyas preferencias coinciden
        try {
            $creator = auth()->user();
            if ($creator && in_array($creator->role, ['admin', 'operator'])) {
                $labelIds = $request->preferences;

                // Obtener usuarios que tienen al menos una de las preferencias seleccionadas
                $users = User::whereHas('preferences', function ($q) use ($labelIds) {
                    $q->whereIn('preferences.id', $labelIds);
                })->whereNotNull('email')->get();

                foreach ($users as $user) {
                    // Obtener las preferencias que coinciden para pasar a la vista del correo
                    $matchedPreferences = $user->preferences()->whereIn('preferences.id', $labelIds)->get();

                    Mail::to($user->email)->send(new NewPlaceNotification($place, $matchedPreferences));
                }
            }
        } catch (\Exception $e) {
            // No detener el flujo si falla el envío; registrar si es necesario
            logger()->error('Error sending new place notifications: ' . $e->getMessage());
        }

    }

    public function favoritos($id){
        $user = auth()->user();
        $place = TuristicPlace::findOrFail($id);

         $user->favoritePlaces()->attach($id);
        return redirect()->back()->with('success', 'Sitio añadido a favoritos.');
    }
    public function removeFavorite($id)
    {
        auth()->user()->favoritePlaces()->detach($id);
        
        return back()->with('success', 'Eliminado de favoritos');
    }

    public function versitiosfavoritos(){
        $user = auth()->user();
        $favoritePlaces = $user->favoritePlaces;
        return view('sitios_ecoturisticos.Sitios_favoritos', compact('favoritePlaces'));
    }

    public function coleccion(Request $request)
{
    $search = $request->input('search');
    
    if ($search) {
        
        $places = TuristicPlace::where('name', 'LIKE', "%{$search}%")
                               ->orWhere('description', 'LIKE', "%{$search}%")
                               ->orWhere('localization', 'LIKE', "%{$search}%")
                               ->get();
    } else {
        
        $places = TuristicPlace::all();
    }
    
    return view('sitios_ecoturisticos.Coleccion', compact('places', 'search'));
}


}
