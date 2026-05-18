<?php

namespace Infrastructure\Repositories;

use App\Models\Contact;
use Domain\Repositories\ContactRepositoryInterface;

class EloquentContactRepository implements ContactRepositoryInterface
{
    public function findById(int $id): ?Contact
    {
        return Contact::find($id);
    }

    public function save(Contact $contact): Contact
    {
        $contact->save();
        return $contact;
    }

    public function listAll(int $perPage = 15): mixed
    {
        return Contact::paginate($perPage);
    }

    public function delete(int $id): bool
    {
        $contact = Contact::find($id);
        if (!$contact) {
            return false;
        }
        return $contact->delete();
    }
}
