<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(User $user): array
    {
        $token = $user->createToken('test')->plainTextToken;
        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_usuario_autenticado_puede_obtener_perfil(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson('/api/profile', $this->authHeaders($user));

        $response->assertStatus(200)
                 ->assertJsonStructure(['id', 'name', 'email']);
    }

    public function test_usuario_no_autenticado_no_puede_obtener_perfil(): void
    {
        $response = $this->getJson('/api/profile');

        $response->assertStatus(401);
    }

    public function test_usuario_puede_actualizar_perfil(): void
    {
        $user = User::factory()->create();

        $response = $this->putJson('/api/profile', [
            'name' => 'Updated Name',
            'last_name' => 'Updated Last',
            'email' => $user->email,
        ], $this->authHeaders($user));

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Perfil actualizado']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_usuario_puede_cambiar_contrasena(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/profile/password', [
            'current_password' => 'Password1',
            'password' => 'NewPassword2',
            'password_confirmation' => 'NewPassword2',
        ], $this->authHeaders($user));

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Contraseña actualizada']);
    }

    public function test_usuario_no_puede_cambiar_contrasena_con_actual_incorrecta(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/profile/password', [
            'current_password' => 'WrongPassword1',
            'password' => 'NewPassword2',
            'password_confirmation' => 'NewPassword2',
        ], $this->authHeaders($user));

        $response->assertStatus(422);
    }

    public function test_usuario_puede_obtener_usuario_actual(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson('/api/user', $this->authHeaders($user));

        $response->assertStatus(200)
                 ->assertJsonFragment(['email' => $user->email]);
    }
}
