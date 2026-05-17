<?php

namespace Tests\Feature\Infrastructure\Repositories;

use App\Models\Contact;
use Infrastructure\Repositories\EloquentContactRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentContactRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentContactRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentContactRepository();
    }

    public function test_should_save_contact(): void
    {
        $contact = new Contact([
            'name' => 'Bruno Silva',
            'email' => 'bruno@example.com',
            'phone' => '11999999999',
            'status' => 'pending',
            'score' => 0
        ]);

        $savedContact = $this->repository->save($contact);

        $this->assertDatabaseHas('contacts', [
            'id' => $savedContact->id,
            'email' => 'bruno@example.com'
        ]);
    }

    public function test_should_find_contact_by_id(): void
    {
        $contact = Contact::factory()->create();

        $foundContact = $this->repository->findById($contact->id);

        $this->assertNotNull($foundContact);
        $this->assertEquals($contact->id, $foundContact->id);
    }

    public function test_should_return_null_when_contact_not_found(): void
    {
        $foundContact = $this->repository->findById(999);
        $this->assertNull($foundContact);
    }

    public function test_should_list_contacts_with_pagination(): void
    {
        Contact::factory()->count(20)->create();

        $list = $this->repository->listAll(10);

        $this->assertCount(10, $list);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $list);
    }

    public function test_should_delete_contact_softly(): void
    {
        $contact = Contact::factory()->create();

        $result = $this->repository->delete($contact->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
    }
}
