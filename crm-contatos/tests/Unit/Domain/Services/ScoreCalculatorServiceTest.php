<?php

namespace Tests\Unit\Domain\Services;

use Domain\Services\ScoreCalculatorService;
use Domain\ValueObjects\Email;
use Domain\ValueObjects\Name;
use Domain\ValueObjects\Phone;
use PHPUnit\Framework\TestCase;

class ScoreCalculatorServiceTest extends TestCase
{
    public function test_should_calculate_perfect_score_from_sao_paulo(): void
    {
        // Regras:
        // Corporate (+20), .br (+10) -> 30
        // Full Name (+10) -> 10
        // SP Phone (+20) -> 20
        // Total: 60

        $name = new Name('Bruno Melo');
        $email = new Email('bruno@empresa.com.br');
        $phone = new Phone('11999999999');

        $service = new ScoreCalculatorService();
        $score = $service->calculate($name, $email, $phone);

        $this->assertEquals(60, $score);
    }

    public function test_should_calculate_minimum_score_from_other_state(): void
    {
        // Regras:
        // Personal (+0), .com (+0) -> 0
        // Single Name (+0) -> 0
        // Other State Phone (+10) -> 10
        // Total: 10

        $name = new Name('Bruno');
        $email = new Email('bruno@gmail.com');
        $phone = new Phone('21999999999');

        $service = new ScoreCalculatorService();
        $score = $service->calculate($name, $email, $phone);

        $this->assertEquals(10, $score);
    }
}
