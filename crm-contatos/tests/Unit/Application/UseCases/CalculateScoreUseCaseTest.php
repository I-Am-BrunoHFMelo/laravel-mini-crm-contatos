<?php

namespace Tests\Unit\Application\UseCases;

use Application\UseCases\CalculateScoreUseCase;
use App\Models\Contact;
use Domain\Repositories\ContactRepositoryInterface;
use Domain\Services\ScoreCalculatorService;
use Tests\TestCase;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CalculateScoreUseCaseTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_should_calculate_and_update_score_successfully(): void
    {
        $repository = Mockery::mock(ContactRepositoryInterface::class);
        $scoreService = Mockery::mock(ScoreCalculatorService::class);

        $useCase = new CalculateScoreUseCase($repository, $scoreService);

        // Criamos no DB para que o Eloquent não reclame
        $contact = Contact::factory()->create([
            'name' => 'Bruno Silva',
            'email' => 'bruno@example.com',
            'phone' => '11999999999',
            'status' => 'pending'
        ]);

        $repository->shouldReceive('findById')->with($contact->id)->andReturn($contact);

        // No mock do repositório, o save apenas retorna o objeto
        $repository->shouldReceive('save')->andReturn($contact);

        $scoreService->shouldReceive('calculate')->once()->andReturn(60);

        $result = $useCase->execute($contact->id);

        $this->assertEquals(60, $result->score);
        $this->assertEquals('active', $result->status);
    }
}
