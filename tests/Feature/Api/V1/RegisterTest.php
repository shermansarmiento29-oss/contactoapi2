<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_se_puede_registrar_y_recibe_un_token(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Ana Pérez',
            'email' => 'ana@example.com',
            'password' => 'password123',
            'fecha' => '2000-05-10',
            'numero' => '3001234567',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('user.name', 'Ana Pérez')
            ->assertJsonPath('user.fecha', '2000-05-10')
            ->assertJsonPath('user.numero', '3001234567')
            ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('users', ['email' => 'ana@example.com']);
    }

    public function test_no_se_puede_registrar_un_correo_repetido(): void
    {
        User::factory()->create(['email' => 'ana@example.com']);

        $response = $this->postJson('/api/v1/register', [
            'name' => 'Otra Ana',
            'email' => 'ana@example.com',
            'password' => 'password123',
            'fecha' => '2000-05-10',
            'numero' => '3001234567',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }
}
