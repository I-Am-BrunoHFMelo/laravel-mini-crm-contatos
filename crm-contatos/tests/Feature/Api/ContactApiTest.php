<?php

namespace Tests\Feature\Api;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessContactScore;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_list_contacts(): void
    {
        Contact::factory()->count(5)->create();

        $response = $this->getJson('/api/contacts');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_should_create_contact(): void
    {
        $data = [
            'name' => 'Bruno Silva',
            'email' => 'bruno@example.com',
            'phone' => '11999999999'
        ];

        $response = $this->postJson('/api/contacts', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Bruno Silva');

        $this->assertDatabaseHas('contacts', ['email' => 'bruno@example.com']);
    }

    public function test_should_show_contact(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->getJson("/api/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $contact->id);
    }

    public function test_should_update_contact(): void
    {
        $contact = Contact::factory()->create(['name' => 'Old Name']);

        $response = $this->putJson("/api/contacts/{$contact->id}", [
            'name' => 'New Name',
            'email' => $contact->email,
            'phone' => $contact->phone
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'New Name');

        $this->assertDatabaseHas('contacts', ['id' => $contact->id, 'name' => 'New Name']);
    }

    public function test_should_delete_contact(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->deleteJson("/api/contacts/{$contact->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
    }

    public function test_should_trigger_score_processing(): void
    {
        Queue::fake();
        $contact = Contact::factory()->create();

        $response = $this->postJson("/api/contacts/{$contact->id}/process-score");

        $response->assertStatus(202);
        Queue::assertPushed(ProcessContactScore::class, function ($job) use ($contact) {
            return $job->contactId === $contact->id;
        });
    }
}
