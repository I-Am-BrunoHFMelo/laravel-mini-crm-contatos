<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use Application\UseCases\CreateContactUseCase;
use Domain\Repositories\ContactRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactController extends Controller
{
    private ContactRepositoryInterface $repository;
    private CreateContactUseCase $createUseCase;

    public function __construct(
        ContactRepositoryInterface $repository,
        CreateContactUseCase $createUseCase
    ) {
        $this->repository = $repository;
        $this->createUseCase = $createUseCase;
    }

    public function index(): AnonymousResourceCollection
    {
        return ContactResource::collection($this->repository->listAll());
    }

    public function store(StoreContactRequest $request): ContactResource
    {
        $contact = $this->createUseCase->execute($request->validated());
        return new ContactResource($contact);
    }

    public function show(int $id): ContactResource
    {
        $contact = $this->repository->findById($id);
        
        if (!$contact) {
            abort(404, 'Contato não encontrado.');
        }

        return new ContactResource($contact);
    }

    public function update(UpdateContactRequest $request, int $id): ContactResource
    {
        $contact = $this->repository->findById($id);

        if (!$contact) {
            abort(404, 'Contato não encontrado.');
        }

        $contact->fill($request->validated());
        $this->repository->save($contact);

        return new ContactResource($contact);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            abort(404, 'Contato não encontrado.');
        }

        return response()->json(null, 204);
    }
}
