<?php

namespace Domain\ValueObjects;

use InvalidArgumentException;

class Phone
{
    private string $value;

    public function __construct(string $value)
    {
        // Limpa tudo que não for número (ex: (11) 99999-9999 vira 11999999999)
        $this->value = preg_replace('/\D/', '', $value);
        
        if (strlen($this->value) < 10 || strlen($this->value) > 11) {
            throw new InvalidArgumentException("Telefone inválido. Deve conter DDD e número.");
        }
    }

    public function getAreaCode(): string
    {
        return substr($this->value, 0, 2);
    }

    public function isFromSaoPaulo(): bool
    {
        $ddd = (int) $this->getAreaCode();
        return $ddd >= 11 && $ddd <= 19;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
