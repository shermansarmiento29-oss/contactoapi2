<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_usuario_puede_ver_su_perfil(): void
    {
        $user = User::factory()->create([
            'name' => 'Sherman',
            'fecha' => '1999-03-15',
            'numero' => '3109876543',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/profile');

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Sherman')
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.fecha', '1999-03-15')
            ->assertJsonPath('data.numero', '3109876543');
    }

    public function test_el_perfil_requiere_token(): void
    {
        $response = $this->getJson('/api/v1/profile');

        $response->assertStatus(401)
            ->assertJsonPath('error.message', 'No autenticado. Token inválido o ausente.');
    }
}
