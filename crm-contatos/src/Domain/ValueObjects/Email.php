<?php

namespace Domain\ValueObjects;

use InvalidArgumentException;

class Email
{
    private string $value;

    public function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("E-mail inválido.");
        }
        $this->value = $value;
    }

    public function isCorporate(): bool
    {
        $domain = substr(strrchr($this->value, "@"), 1);
        $commonDomains = ['gmail.com', 'hotmail.com', 'yahoo.com', 'outlook.com', 'live.com'];
        return !in_array(strtolower($domain), $commonDomains);
    }

    public function isBrazilian(): bool
    {
        return str_ends_with(strtolower($this->value), '.br');
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
