<?php

namespace Tests\Feature;

use App\Models\TuristicPlace;
use App\Models\User;
use App\Models\reviews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewsApiTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(User $user): array
    {
        $token = $user->createToken('test')->plainTextToken;
        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_user_can_create_review(): void
    {
        $user = User::factory()->create();
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $response = $this->postJson("/api/places/{$place->id}/reviews", [
            'rating' => 5,
            'comment' => 'Un lugar increíble, muy recomendado para visitar',
        ], $this->authHeaders($user));

        $response->assertStatus(201)
            ->assertJson(['message' => 'Reseña creada exitosamente']);

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'place_id' => $place->id,
            'rating' => 5,
        ]);
    }

    public function test_operator_cannot_review_own_place(): void
    {
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $response = $this->postJson("/api/places/{$place->id}/reviews", [
            'rating' => 5,
            'comment' => 'Mi propio sitio es genial y recomendado',
        ], $this->authHeaders($operator));

        $response->assertStatus(403);
    }

    public function test_review_requires_rating(): void
    {
        $user = User::factory()->create();
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $response = $this->postJson("/api/places/{$place->id}/reviews", [
            'comment' => 'Un comentario sin rating para el sitio',
        ], $this->authHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }

    public function test_review_rating_must_be_between_1_and_5(): void
    {
        $user = User::factory()->create();
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $response = $this->postJson("/api/places/{$place->id}/reviews", [
            'rating' => 6,
        ], $this->authHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }

    public function test_user_can_delete_own_review(): void
    {
        $user = User::factory()->create();
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $review = reviews::create([
            'user_id' => $user->id,
            'place_id' => $place->id,
            'rating' => 4,
            'comment' => 'Un buen lugar para toda la familia',
        ]);

        $response = $this->deleteJson("/api/reviews/{$review->id}", [], $this->authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Reseña eliminada exitosamente']);
    }

    public function test_user_cannot_delete_others_review(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $review = reviews::create([
            'user_id' => $user1->id,
            'place_id' => $place->id,
            'rating' => 4,
        ]);

        $response = $this->deleteJson("/api/reviews/{$review->id}", [], $this->authHeaders($user2));

        $response->assertStatus(403);
    }

    public function test_user_can_react_to_review(): void
    {
        $user = User::factory()->create();
        $reviewer = User::factory()->create();
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $review = reviews::create([
            'user_id' => $reviewer->id,
            'place_id' => $place->id,
            'rating' => 5,
        ]);

        $response = $this->postJson("/api/reviews/{$review->id}/react", [
            'type' => 'like',
        ], $this->authHeaders($user));

        $response->assertStatus(201)
            ->assertJson(['message' => 'Reacción agregada']);
    }

    public function test_unauthenticated_user_cannot_create_review(): void
    {
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $response = $this->postJson("/api/places/{$place->id}/reviews", [
            'rating' => 5,
            'comment' => 'Un lugar increíble para toda la familia',
        ]);

        $response->assertStatus(401);
    }

    public function test_review_rejects_profanity(): void
    {
        $user = User::factory()->create();
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $response = $this->postJson("/api/places/{$place->id}/reviews", [
            'rating' => 1,
            'comment' => 'Este lugar es una mierda total y no lo recomiendo',
        ], $this->authHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['comment']);
    }
}
