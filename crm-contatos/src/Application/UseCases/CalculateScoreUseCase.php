<?php

namespace Application\UseCases;

use App\Models\Contact;
use Domain\Repositories\ContactRepositoryInterface;
use Domain\Services\ScoreCalculatorService;
use Domain\ValueObjects\Email;
use Domain\ValueObjects\Name;
use Domain\ValueObjects\Phone;
use Domain\ValueObjects\Status;
use Exception;

class CalculateScoreUseCase
{
    private ContactRepositoryInterface $repository;
    private ScoreCalculatorService $scoreService;

    public function __construct(
        ContactRepositoryInterface $repository,
        ScoreCalculatorService $scoreService
    ) {
        $this->repository = $repository;
        $this->scoreService = $scoreService;
    }

    public function execute(int $contactId): Contact
    {
        $contact = $this->repository->findById($contactId);

        if (!$contact) {
            throw new Exception("Contato não encontrado.");
        }

        try {
            // 1. Inicia processamento
            $contact->status = (string) Status::processing();
            $this->repository->save($contact);

            // Simulação de delay conforme README
            sleep(2);

            // 2. Cria Value Objects para o cálculo
            $name = new Name($contact->name);
            $email = new Email($contact->email);
            $phone = new Phone($contact->phone);

            // 3. Calcula Score
            $score = $this->scoreService->calculate($name, $email, $phone);

            // 4. Finaliza
            $contact->score = $score;
            $contact->status = (string) Status::active();
            $contact->processed_at = now();
            $this->repository->save($contact);

            event(new \App\Events\ContactScoreProcessed($contact));

            return $contact;
        } catch (Exception $e) {
            $contact->status = (string) Status::failed();
            $this->repository->save($contact);
            throw $e;
        }
    }
}
