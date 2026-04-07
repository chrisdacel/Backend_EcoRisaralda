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
                    $badWords = [
                        'puta','puto','mierda','joder','gilipollas','pendejo','cabron','coño','marica','imbecil','idiota','culero','zorra','perra','malparido','hijueputa','verga','chingar','cabrón','pendeja','estupido','estúpido','estupida','estúpida',
                        'fuck','shit','bitch','asshole','bastard','dick','cunt','fag','faggot','slut','whore','motherfucker','douche','douchebag','bollocks','bugger','bloody','wanker','prick','twat','jerk','moron','retard','suck','damn','crap','pussy','cock','arse','arsehole','nigger','nigga','spic','chink','kike','fucker','fucking','fucks','fucked','faggot',
                    ];
                    $text = mb_strtolower($value, 'UTF-8');
                    foreach ($badWords as $bad) {
                        if (strpos($text, $bad) !== false) {
                            $fail('El comentario contiene lenguaje inapropiado. Por favor, utiliza un lenguaje respetuoso.');
                            return;
                        }
                    }
                    try {
                        if (class_exists(\Waad\ProfanityFilter\Facades\ProfanityFilter::class) && \Waad\ProfanityFilter\Facades\ProfanityFilter::hasProfanity($value)) {
                            $fail('El comentario contiene lenguaje inapropiado. Por favor, utiliza un lenguaje respetuoso.');
                        }
                    } catch (\Throwable $e) {}
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
                    $badWords = [
                        'puta','puto','mierda','joder','gilipollas','pendejo','cabron','coño','marica','imbecil','idiota','culero','zorra','perra','malparido','hijueputa','verga','chingar','cabrón','pendeja','estupido','estúpido','estupida','estúpida',
                        'fuck','shit','bitch','asshole','bastard','dick','cunt','fag','faggot','slut','whore','motherfucker','douche','douchebag','bollocks','bugger','bloody','wanker','prick','twat','jerk','moron','retard','suck','damn','crap','pussy','cock','arse','arsehole','nigger','nigga','spic','chink','kike','fucker','fucking','fucks','fucked','faggot',
                    ];
                    $text = mb_strtolower($value, 'UTF-8');
                    foreach ($badWords as $bad) {
                        if (strpos($text, $bad) !== false) {
                            $fail('El comentario contiene lenguaje inapropiado. Por favor, utiliza un lenguaje respetuoso.');
                            return;
                        }
                    }
                    try {
                        if (class_exists(\Waad\ProfanityFilter\Facades\ProfanityFilter::class) && \Waad\ProfanityFilter\Facades\ProfanityFilter::hasProfanity($value)) {
                            $fail('El comentario contiene lenguaje inapropiado. Por favor, utiliza un lenguaje respetuoso.');
                        }
                    } catch (\Throwable $e) {}
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
