<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Waad\ProfanityFilter\Facades\ProfanityFilter;

class CommentController extends Controller
{
    /**
     * POST /api/places/{id}/comments
     * Create a new comment without rating (authenticated user)
     */
    public function store(Request $request, $placeId)
    {
        // Limitar a 2 comentarios por usuario por sitio al día
        $today = now()->startOfDay();
        $commentsToday = Comment::where('user_id', $request->user()->id)
            ->where('place_id', $placeId)
            ->where('created_at', '>=', $today)
            ->count();
        if ($commentsToday >= 2) {
            return response()->json([
                'message' => 'Solo puedes hacer hasta 2 comentarios por día en este sitio.'
            ], 429);
        }

        // Validar comentario
        $validated = $request->validate([
            'comment' => [
                'required',
                'string',
                'min:10',
                'max:1000',
                function ($attribute, $value, $fail) {
                    if (ProfanityFilter::hasProfanity($value)) {
                        $fail('El comentario contiene lenguaje inapropiado. Por favor, utiliza un lenguaje respetuoso.');
                    }
                }
            ],
        ]);

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'place_id' => $placeId,
            'comment' => $validated['comment'],
        ]);

        $comment->load('user');

        return response()->json([
            'message' => 'Comentario creado exitosamente',
            'comment' => $comment,
        ], 201);
    }

    /**
     * PUT /api/comments/{id}
     * Update a comment (authenticated user, only owner)
     */
    public function update(Request $request, $commentId)
    {
        $comment = Comment::findOrFail($commentId);

        // Verificar que sea el dueño del comentario
        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para editar este comentario.'
            ], 403);
        }

        $validated = $request->validate([
            'comment' => [
                'required',
                'string',
                'min:10',
                'max:1000',
                function ($attribute, $value, $fail) {
                    if (ProfanityFilter::hasProfanity($value)) {
                        $fail('El comentario contiene lenguaje inapropiado. Por favor, utiliza un lenguaje respetuoso.');
                    }
                }
            ],
        ]);

        $comment->update([
            'comment' => $validated['comment'],
        ]);

        $comment->load('user');

        return response()->json([
            'message' => 'Comentario actualizado exitosamente',
            'comment' => $comment,
        ]);
    }

    /**
     * DELETE /api/comments/{id}
     * Delete a comment (authenticated user, only owner or admin)
     */
    public function destroy(Request $request, $commentId)
    {
        $comment = Comment::findOrFail($commentId);

        // Verificar que sea el dueño del comentario o un admin
        if ($comment->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'No tienes permiso para eliminar este comentario.'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comentario eliminado exitosamente'
        ]);
    }
}
