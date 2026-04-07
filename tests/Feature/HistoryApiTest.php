<?php

namespace Tests\Feature;

use App\Models\TuristicPlace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HistoryApiTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(User $user): array
    {
        $token = $user->createToken('test')->plainTextToken;
        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_user_can_register_place_visit(): void
    {
        $user = User::factory()->create();
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $response = $this->postJson("/api/places/{$place->id}/visit", [], $this->authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Visita registrada']);

        $this->assertDatabaseHas('user_place_visits', [
            'user_id' => $user->id,
            'place_id' => $place->id,
        ]);
    }

    public function test_user_can_get_history(): void
    {
        $user = User::factory()->create();
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        // Register a visit first
        DB::table('user_place_visits')->insert([
            'user_id' => $user->id,
            'place_id' => $place->id,
            'visited_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/user/history', $this->authHeaders($user));

        $response->assertStatus(200);
    }

    public function test_user_can_get_their_reviews(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson('/api/user/reviews', $this->authHeaders($user));

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_history(): void
    {
        $response = $this->getJson('/api/user/history');

        $response->assertStatus(401);
    }
}
