<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessContactScore;
use Domain\Repositories\ContactRepositoryInterface;
use Illuminate\Http\JsonResponse;

class ProcessScoreController extends Controller
{
    private ContactRepositoryInterface $repository;

    public function __construct(ContactRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(int $id): JsonResponse
    {
        $contact = $this->repository->findById($id);

        if (!$contact) {
            abort(404, 'Contato não encontrado.');
        }

        ProcessContactScore::dispatch($id);

        return response()->json([
            'message' => 'Processamento do score iniciado.',
            'status' => 'processing'
        ], 202);
    }
}
