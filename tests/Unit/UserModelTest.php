<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\TuristicPlace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_tiene_atributos_llenables(): void
    {
        $user = new User();

        $this->assertContains('name', $user->getFillable());
        $this->assertContains('email', $user->getFillable());
        $this->assertContains('password', $user->getFillable());
        $this->assertContains('role', $user->getFillable());
        $this->assertContains('last_name', $user->getFillable());
    }

    public function test_usuario_oculta_atributos_sensibles(): void
    {
        $user = new User();

        $this->assertContains('password', $user->getHidden());
        $this->assertContains('remember_token', $user->getHidden());
    }

    public function test_usuario_convierte_email_verificado_a_datetime(): void
    {
        $user = new User();
        $casts = $user->getCasts();

        $this->assertArrayHasKey('email_verified_at', $casts);
    }

    public function test_contrasena_de_usuario_esta_hasheada(): void
    {
        $user = User::factory()->create(['password' => 'TestPassword1']);

        $this->assertNotEquals('TestPassword1', $user->password);
    }

    public function test_usuario_tiene_relacion_de_preferencias(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->preferences());
    }

    public function test_usuario_tiene_relacion_de_lugares_favoritos(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->favoritePlaces());
    }

    public function test_factory_de_usuario_crea_usuario_valido(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->id);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->email_verified_at);
        $this->assertEquals('user', $user->role);
    }

    public function test_factory_de_usuario_puede_crear_operador(): void
    {
        $operator = User::factory()->operator()->create();

        $this->assertEquals('operator', $operator->role);
    }

    public function test_factory_de_usuario_puede_crear_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertEquals('admin', $admin->role);
    }
}
