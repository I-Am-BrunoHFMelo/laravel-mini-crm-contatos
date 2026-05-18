<?php

namespace App\Jobs;

use Application\UseCases\CalculateScoreUseCase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessContactScore implements ShouldQueue
{
    use Queueable;

    public int $contactId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $contactId)
    {
        $this->contactId = $contactId;
    }

    /**
     * Execute the job.
     */
    public function handle(CalculateScoreUseCase $useCase): void
    {
        $useCase->execute($this->contactId);
    }
}
