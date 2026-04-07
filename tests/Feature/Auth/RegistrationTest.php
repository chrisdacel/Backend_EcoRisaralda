<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_via_api(): void
    {
        $country = \App\Models\Country::factory()->create();
        $response = $this->postJson('/api/register', [
            'name' => 'Juan',
            'last_name' => 'Pérez',
            'email' => 'juan@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'turist',
            'country' => $country->id,
            'birth_date' => '1990-01-01',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Registro exitoso. Revisa tu correo para verificar la cuenta.']);

        $this->assertDatabaseHas('users', [
            'email' => 'juan@example.com',
            'role' => 'user', // 'turist' gets mapped to 'user'
        ]);
    }

    public function test_operator_can_register_via_api(): void
    {
        $country = \App\Models\Country::factory()->create();
        $response = $this->postJson('/api/register', [
            'name' => 'Maria',
            'last_name' => 'Gomez',
            'email' => 'maria@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'operator',
            'country' => $country->id,
            'birth_date' => '1985-05-15',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'email' => 'maria@example.com',
            'role' => 'operator',
        ]);
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'Test',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'password' => 'Password123',
            'role' => 'turist',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_with_weak_password(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'weak', // No uppercase, no digit, too short
            'role' => 'turist',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_requires_last_name(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'role' => 'turist',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['last_name']);
    }
}
