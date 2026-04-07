<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreferencesApiTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(User $user): array
    {
        $token = $user->createToken('test')->plainTextToken;
        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_can_list_preferences_without_auth(): void
    {
        $response = $this->getJson('/api/preferences');

        $response->assertStatus(200);
    }

    public function test_preferences_are_seeded_if_empty(): void
    {
        $response = $this->getJson('/api/preferences');

        $response->assertStatus(200)
            ->assertJsonCount(8); // 8 default preferences
    }

    public function test_user_can_get_their_preferences(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson('/api/user/preferences', $this->authHeaders($user));

        $response->assertStatus(200);
    }

    public function test_user_can_update_preferences(): void
    {
        $user = User::factory()->create();

        // First, ensure preferences exist
        $this->getJson('/api/preferences');

        // Get first preference ID
        $preferences = \App\Models\preference::pluck('id')->take(3)->toArray();

        $response = $this->postJson('/api/user/preferences', [
            'preferences' => $preferences,
        ], $this->authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Preferencias actualizadas']);
    }

    public function test_first_time_preferences_flag(): void
    {
        $user = User::factory()->create(['first_time_preferences' => true]);

        $response = $this->getJson('/api/user/first-time-preferences', $this->authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['first_time' => true]);
    }

    public function test_unauthenticated_user_cannot_update_preferences(): void
    {
        $response = $this->postJson('/api/user/preferences', [
            'preferences' => [1, 2],
        ]);

        $response->assertStatus(401);
    }
}
