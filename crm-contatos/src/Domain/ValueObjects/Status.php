<?php

namespace Domain\ValueObjects;

use InvalidArgumentException;

class Status
{
    public const PENDING = 'pending';
    public const PROCESSING = 'processing';
    public const ACTIVE = 'active';
    public const FAILED = 'failed';

    private string $value;

    public function __construct(string $value)
    {
        $value = strtolower(trim($value));

        if (!in_array($value, self::allowedValues(), true)) {
            throw new InvalidArgumentException("Status inválido: {$value}");
        }

        $this->value = $value;
    }

    public static function pending(): self
    {
        return new self(self::PENDING);
    }

    public static function processing(): self
    {
        return new self(self::PROCESSING);
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public static function failed(): self
    {
        return new self(self::FAILED);
    }

    public static function allowedValues(): array
    {
        return [
            self::PENDING,
            self::PROCESSING,
            self::ACTIVE,
            self::FAILED,
        ];
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
