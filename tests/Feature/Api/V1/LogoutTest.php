<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_logout_revoca_el_token_actual(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/logout');

        $response->assertStatus(204);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
