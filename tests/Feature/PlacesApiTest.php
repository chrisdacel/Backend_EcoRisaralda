<?php

namespace Tests\Feature;

use App\Models\TuristicPlace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlacesApiTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(User $user): array
    {
        $token = $user->createToken('test')->plainTextToken;
        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_can_list_places(): void
    {
        $operator = User::factory()->operator()->create();
        TuristicPlace::factory()->count(3)->create(['user_id' => $operator->id]);

        $response = $this->getJson('/api/places');

        $response->assertStatus(200);
    }

    public function test_can_get_single_place(): void
    {
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $response = $this->getJson("/api/places/{$place->id}");

        $response->assertStatus(200);
    }

    public function test_returns_404_for_nonexistent_place(): void
    {
        $response = $this->getJson('/api/places/99999');

        $response->assertStatus(404);
    }

    public function test_operator_can_get_their_places(): void
    {
        $operator = User::factory()->operator()->create();
        TuristicPlace::factory()->count(2)->create(['user_id' => $operator->id]);

        $response = $this->getJson('/api/user-places', $this->authHeaders($operator));

        $response->assertStatus(200);
    }

    public function test_operator_can_delete_own_place(): void
    {
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $response = $this->deleteJson("/api/places/{$place->id}", [], $this->authHeaders($operator));

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_delete_place(): void
    {
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $response = $this->deleteJson("/api/places/{$place->id}");

        $response->assertStatus(401);
    }
}
