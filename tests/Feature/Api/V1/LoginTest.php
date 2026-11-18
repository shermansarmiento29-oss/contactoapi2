<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_puede_iniciar_sesion_con_credenciales_correctas(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'token_type']);
    }

    public function test_el_login_falla_con_contrasena_incorrecta(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'contrasena-mala',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'INVALID_CREDENTIALS');
    }
}
