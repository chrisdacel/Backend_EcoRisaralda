<?php

namespace Tests\Feature;

use App\Models\TuristicPlace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesApiTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(User $user): array
    {
        $token = $user->createToken('test')->plainTextToken;
        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_user_can_list_favorites(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson('/api/favorites', $this->authHeaders($user));

        $response->assertStatus(200);
    }

    public function test_user_can_add_to_favorites(): void
    {
        $user = User::factory()->create();
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $response = $this->postJson("/api/places/{$place->id}/favorite", [], $this->authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Agregado a favoritos']);
    }

    public function test_user_can_remove_from_favorites(): void
    {
        $user = User::factory()->create();
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        // Add first
        $user->favoritePlaces()->syncWithoutDetaching([$place->id]);

        $response = $this->deleteJson("/api/places/{$place->id}/favorite", [], $this->authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Eliminado de favoritos']);
    }

    public function test_unauthenticated_user_cannot_manage_favorites(): void
    {
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $response = $this->postJson("/api/places/{$place->id}/favorite");

        $response->assertStatus(401);
    }
}
