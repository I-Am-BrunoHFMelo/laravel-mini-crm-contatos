<?php

namespace Application\UseCases;

use App\Models\Contact;
use Domain\Repositories\ContactRepositoryInterface;
use Domain\ValueObjects\Status;

class CreateContactUseCase
{
    private ContactRepositoryInterface $repository;

    public function __construct(ContactRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $data): Contact
    {
        $contact = new Contact([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'status' => (string) Status::pending(),
            'score' => 0,
        ]);

        return $this->repository->save($contact);
    }
}
