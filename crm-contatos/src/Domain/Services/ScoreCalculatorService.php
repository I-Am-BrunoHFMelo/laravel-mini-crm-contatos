<?php

namespace Domain\Services;

use Domain\ValueObjects\Email;
use Domain\ValueObjects\Name;
use Domain\ValueObjects\Phone;

class ScoreCalculatorService
{
    public function calculate(Name $name, Email $email, Phone $phone): int
    {
        $score = 0;

        // Regras de E-mail
        if ($email->isCorporate()) {
            $score += 20;
        }
        if ($email->isBrazilian()) {
            $score += 10;
        }

        // Regras de Nome
        if ($name->isFullName()) {
            $score += 10;
        }

        // Regras de Telefone
        if ($phone->isFromSaoPaulo()) {
            $score += 20;
        } else {
            $score += 10; // Outros estados
        }

        return $score;
    }
}
