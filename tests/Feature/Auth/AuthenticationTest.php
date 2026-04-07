<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_via_api(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'Password1',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token', 'message']);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'WrongPassword1',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Credenciales incorrectas']);
    }

    public function test_unverified_user_cannot_login(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'unverified@example.com',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'unverified@example.com',
            'password' => 'Password1',
        ]);

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Sesión cerrada']);

        // Verify token was revoked
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_login_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'status' => 'inactive',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'inactive@example.com',
            'password' => 'Password1',
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Tu cuenta ha sido desactivada. Por favor, contacta con el administrador.']);
    }
}

