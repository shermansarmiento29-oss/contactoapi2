<?php

namespace Tests\Feature\Api\V1;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_access_contacts_endpoints(): void
    {
        $response = $this->getJson('/api/v1/contacts');

        $response->assertStatus(401)
                 ->assertJson([
                     'error' => [
                         'status' => 401,
                         'code' => 'UNAUTHORIZED',
                     ]
                 ]);
    }

    public function test_authenticated_user_can_list_only_their_own_contacts(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Contact::factory(3)->create(['user_id' => $user1->id]);
        Contact::factory(2)->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1, 'sanctum')
                         ->getJson('/api/v1/contacts');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_authenticated_user_can_create_a_contact(): void
    {
        $user = User::factory()->create();

        $payload = [
            'name' => 'Carlos Mendoza',
            'phone' => '+573001234567',
            'email' => 'carlos@example.com',
        ];

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/v1/contacts', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.name', 'Carlos Mendoza');

        $this->assertDatabaseHas('contacts', [
            'user_id' => $user->id,
            'email' => 'carlos@example.com',
        ]);
    }

    public function test_user_cannot_view_or_modify_contacts_of_another_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $contactUser2 = Contact::factory()->create(['user_id' => $user2->id]);

        $responseShow = $this->actingAs($user1, 'sanctum')
                             ->getJson("/api/v1/contacts/{$contactUser2->id}");

        $responseShow->assertStatus(404);

        $responseDelete = $this->actingAs($user1, 'sanctum')
                               ->deleteJson("/api/v1/contacts/{$contactUser2->id}");

        $responseDelete->assertStatus(404);
    }

    public function test_contact_deletion_performs_soft_delete(): void
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
                         ->deleteJson("/api/v1/contacts/{$contact->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('contacts', [
            'id' => $contact->id,
        ]);
    }
}