<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\reviews;
use Waad\ProfanityFilter\Facades\ProfanityFilter;

class ReviewsController extends Controller
{
    public function publicarreseña(Request $request, $id)
    {
        $request->validate([
            'review' => [
                'required',
                'string',
                'max:1000',
                function ($attribute, $value, $fail) {
                    // Usar hasProfanity() en lugar de isDirty()
                    if (ProfanityFilter::hasProfanity($value)) {
                        $fail('La reseña contiene lenguaje inapropiado. Por favor, utiliza un lenguaje respetuoso.');
                    }
                }
            ],
            'rating' => 'required|integer|between:1,5',
        ], [
            'review.required' => 'No puede enviar una reseña vacía.',
            'rating.required' => 'Envíe una calificación del sitio'
        ]);

        reviews::create([
            'rating' => $request->input('rating'),
            'comment' => $request->input('review'),
            'place_id' => $id,
            'user_id' => auth()->id(),
        ]);
        
        app(\App\Http\Controllers\RateController::class)
            ->promedio($id);
            
        return redirect()->back()->with('success', 'Reseña publicada correctamente.');
    }
    
    public function eliminarreseña($id)
    {
        $review = reviews::findOrFail($id);
        $review->delete();
        
        return redirect()->back()->with('success', 'Reseña eliminada correctamente.');
    }
}