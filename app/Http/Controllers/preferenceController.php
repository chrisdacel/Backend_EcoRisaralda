<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class preferenceController extends Controller
{
    public function mostrardatosdepreferencias(){
        $preferences = \App\Models\preference::all();
        return view('preferencias', compact('preferences'));
    }

    public function validarpreferencias(Request $request){
        $request->validate([
            'preferences' => 'required|array|min:1'
        ],[
            'preferences.required' => 'Debes seleccionar al menos una preferencia.',
    'preferences.min' => 'Selecciona al menos una preferencia.',


        ]
    
        
    );
$user = auth()->user();

$user->preferences()->sync($request->preferences);
return redirect()->route('dashboard');

    }
}
 
