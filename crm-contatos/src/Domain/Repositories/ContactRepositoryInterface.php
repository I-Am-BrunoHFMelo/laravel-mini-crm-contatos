<?php

namespace Domain\Repositories;

use App\Models\Contact;

interface ContactRepositoryInterface
{
    public function findById(int $id): ?Contact;
    public function save(Contact $contact): Contact;
    public function listAll(int $perPage = 15): mixed;
    public function delete(int $id): bool;
}
