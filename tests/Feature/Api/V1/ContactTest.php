<?php

namespace Tests\Feature\Api\V1;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
                ],
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

    public function test_authenticated_user_can_upload_and_replace_contact_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $initialAvatar = UploadedFile::fake()->image('avatar1.jpg');

        // 1. Crear contacto con imagen
        $createResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/contacts', [
                'name' => 'Laura Gómez',
                'address' => 'Calle 10 # 5-20',
                'avatar' => $initialAvatar,
            ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.address', 'Calle 10 # 5-20');

        $contact = Contact::first();
        $firstAvatarPath = $contact->avatar;

        $this->assertNotNull($firstAvatarPath);
        Storage::disk('public')->assertExists($firstAvatarPath);

        // 2. Actualizar imagen (usando POST + _method=PUT)
        $newAvatar = UploadedFile::fake()->image('avatar2.png');

        $updateResponse = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/contacts/{$contact->id}", [
                '_method' => 'PUT',
                'avatar' => $newAvatar,
            ]);

        $updateResponse->assertStatus(200);

        $contact->refresh();
        Storage::disk('public')->assertMissing($firstAvatarPath);
        Storage::disk('public')->assertExists($contact->avatar);
    }
}