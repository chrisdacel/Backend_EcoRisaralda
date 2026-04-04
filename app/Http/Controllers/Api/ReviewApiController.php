<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\reviews;
use App\Models\ReviewReaction;
use Illuminate\Http\Request;
    // use Waad\ProfanityFilter\Facades\ProfanityFilter;

class ReviewApiController extends Controller
{
    /**
     * POST /api/places/{id}/reviews
     * Create a new review (authenticated user)
     */
    public function store(Request $request, $placeId)
    {
        $userId = $request->user()->id;
        $antiSpamMinutes = 5; // Cambia este valor según tu política
        $now = now();

        // Restricción: operador no puede calificar/reseñar sus propios sitios
        $user = $request->user();
        $place = \App\Models\TuristicPlace::find($placeId);
        if ($user && $user->role === 'operator' && $place && $place->user_id == $user->id) {
            return response()->json(['message' => 'No puedes calificar o dejar reseña en tus propios sitios'], 403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => [
                'nullable',
                'string',
                'min:10',
                'max:1000',
                function ($attribute, $value, $fail) {
                    // Filtro de palabras prohibidas (español e inglés)
                    $badWords = [
                        // Español
                        'puta','puto','mierda','joder','gilipollas','pendejo','cabron','coño','marica','imbecil','idiota','culero','zorra','perra','malparido','hijueputa','verga','chingar','cabrón','pendeja','estupido','estúpido','estupida','estúpida',
                        // Inglés
                        'fuck','shit','bitch','asshole','bastard','dick','cunt','fag','faggot','slut','whore','motherfucker','douche','douchebag','bollocks','bugger','bloody','wanker','prick','twat','jerk','moron','retard','suck','damn','crap','pussy','cock','arse','arsehole','nigger','nigga','spic','chink','kike','fucker','fucking','fucks','fucked','faggot',
                    ];
                    if ($value) {
                        $text = mb_strtolower($value, 'UTF-8');
                        foreach ($badWords as $bad) {
                            if (strpos($text, $bad) !== false) {
                                $fail('El comentario contiene lenguaje inapropiado. Por favor, utiliza un lenguaje respetuoso.');
                                break;
                            }
                        }
                    }
                }
            ],
        ]);

        $review = reviews::where('user_id', $userId)
            ->where('place_id', $placeId)
            ->first();

        if ($review) {
            // Permitir editar una vez de manera inmediata después de crearla
            $editCount = $review->edit_count ?? 0;
            // Si es la primera edición, permitirla sin restricción
            if ($editCount >= 1 && $review->updated_at && $review->updated_at->diffInMinutes($now) < $antiSpamMinutes) {
                return response()->json([
                    'message' => 'Debes esperar antes de editar tu reseña nuevamente.'
                ], 429);
            }
            $oldRating = $review->rating;
            $review->rating = $validated['rating'];
            $review->comment = $validated['comment'] ?? null;
            $review->edit_count = $editCount + 1;
            $review->save();
            $review->load('user');
            $review->likes_count = $review->reactions->where('type', 'like')->count();
            $review->dislikes_count = $review->reactions->where('type', 'dislike')->count();
            $review->user_reaction = null;
            $wasLowered = $oldRating > $validated['rating'];
            return response()->json([
                'message' => 'Reseña actualizada exitosamente',
                'review' => $review,
                'was_lowered' => $wasLowered,
                'edited' => $review->updated_at > $review->created_at,
            ], 200);
        } else {
            $review = reviews::create([
                'user_id' => $userId,
                'place_id' => $placeId,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]);
            $review->load('user');
            $review->likes_count = 0;
            $review->dislikes_count = 0;
            $review->user_reaction = null;
            return response()->json([
                'message' => 'Reseña creada exitosamente',
                'review' => $review,
                'was_lowered' => false,
                'edited' => false,
            ], 201);
        }
    }

    /**
     * DELETE /api/reviews/{id}
     * Delete a review (owner or admin only)
     */
    public function destroy(Request $request, $id)
    {
        $review = reviews::findOrFail($id);

        // Authorization check
        if ($request->user()->id !== $review->user_id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Reseña eliminada exitosamente']);
    }

    /**
     * PUT /api/reviews/{id}
     * Update a review (owner or admin only)
     */
    public function update(Request $request, $id)
    {
        $review = reviews::findOrFail($id);
        $userId = $request->user()->id;
        $antiSpamMinutes = 5; // Cambia este valor según tu política
        $now = now();

        // Authorization check
        if ($userId !== $review->user_id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Permitir editar una vez de manera inmediata después de crearla
        $editCount = $review->edit_count ?? 0;
        if ($editCount >= 1 && $review->updated_at && $review->updated_at->diffInMinutes($now) < $antiSpamMinutes) {
            return response()->json([
                'message' => 'Debes esperar antes de editar tu reseña nuevamente.'
            ], 429);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => [
                'nullable',
                'string',
                'min:10',
                'max:1000',
                function ($attribute, $value, $fail) {
                    // if ($value && ProfanityFilter::hasProfanity($value)) {
                    //     $fail('El comentario contiene lenguaje inapropiado. Por favor, utiliza un lenguaje respetuoso.');
                    // }
                }
            ],
        ]);

        $oldRating = $review->rating;
        $review->rating = $validated['rating'];
        $review->comment = $validated['comment'] ?? null;
        $review->edit_count = $editCount + 1;
        $review->save();
        $review->load(['user', 'reactions']);

        // Agregar contadores y reacción del usuario
        $review->likes_count = $review->reactions->where('type', 'like')->count();
        $review->dislikes_count = $review->reactions->where('type', 'dislike')->count();

        $userReaction = $review->reactions->first(function ($reaction) use ($userId) {
            return $reaction->user_id === $userId;
        });
        $review->user_reaction = $userReaction ? $userReaction->type : null;

        unset($review->reactions);

        $wasLowered = $oldRating > $validated['rating'];

        return response()->json([
            'message' => 'Reseña actualizada exitosamente',
            'review' => $review,
            'was_lowered' => $wasLowered,
            'edited' => $review->updated_at > $review->created_at,
        ]);
    }

    /**
     * POST /api/reviews/{id}/react
     * Add or update a reaction (like/dislike) to a review
     */
    public function react(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|in:like,dislike',
        ]);

        $review = reviews::findOrFail($id);
        $userId = $request->user()->id;

        // Buscar reacción existente
        $reaction = ReviewReaction::where('review_id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($reaction) {
            if ($reaction->type === $validated['type']) {
                // Si es la misma reacción, eliminarla (toggle)
                $reaction->delete();
                return response()->json([
                    'message' => 'Reacción eliminada',
                    'reaction' => null,
                ]);
            } else {
                // Si es diferente, actualizarla
                $reaction->type = $validated['type'];
                $reaction->save();
                return response()->json([
                    'message' => 'Reacción actualizada',
                    'reaction' => $reaction,
                ]);
            }
        } else {
            // Crear nueva reacción
            $reaction = ReviewReaction::create([
                'review_id' => $id,
                'user_id' => $userId,
                'type' => $validated['type'],
            ]);
            return response()->json([
                'message' => 'Reacción agregada',
                'reaction' => $reaction,
            ], 201);
        }
    }
}
