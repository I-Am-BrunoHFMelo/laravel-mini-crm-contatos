<?php

namespace Domain\ValueObjects;

use InvalidArgumentException;

class Name
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        if (empty($value)) {
            throw new InvalidArgumentException("O nome não pode ser vazio.");
        }
        $this->value = $value;
    }

    public function isFullName(): bool
    {
        // Se tiver espaço no meio, assumimos que é nome composto/completo
        return str_contains($this->value, ' ');
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
