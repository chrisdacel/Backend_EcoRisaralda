<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_email_can_be_resent(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'unverified@example.com',
        ]);

        $response = $this->postJson('/api/email/verification-notification', [
            'email' => 'unverified@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Enlace de verificación enviado']);
    }

    public function test_verified_user_gets_already_verified_message(): void
    {
        $user = User::factory()->create([
            'email' => 'verified@example.com',
        ]);

        $response = $this->postJson('/api/email/verification-notification', [
            'email' => 'verified@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'El correo ya está verificado']);
    }

    public function test_nonexistent_email_returns_generic_success(): void
    {
        // Should return success to prevent email enumeration
        $response = $this->postJson('/api/email/verification-notification', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Enlace de verificación enviado']);
    }
}
