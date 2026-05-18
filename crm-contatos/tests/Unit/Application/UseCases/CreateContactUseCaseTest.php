<?php

namespace Tests\Unit\Application\UseCases;

use Application\UseCases\CreateContactUseCase;
use App\Models\Contact;
use Domain\Repositories\ContactRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Mockery;

class CreateContactUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_should_create_contact_successfully(): void
    {
        $repository = Mockery::mock(ContactRepositoryInterface::class);
        $useCase = new CreateContactUseCase($repository);

        $data = [
            'name' => 'Bruno Silva',
            'email' => 'bruno@example.com',
            'phone' => '11999999999'
        ];

        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($contact) use ($data) {
                return $contact instanceof Contact &&
                       $contact->name === $data['name'] &&
                       $contact->email === $data['email'] &&
                       $contact->status === 'pending' &&
                       $contact->score === 0;
            }))
            ->andReturn(new Contact($data));

        $result = $useCase->execute($data);

        $this->assertInstanceOf(Contact::class, $result);
        $this->assertEquals($data['email'], $result->email);
    }
}
