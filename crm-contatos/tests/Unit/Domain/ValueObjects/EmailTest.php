<?php

namespace Tests\Unit\Domain\ValueObjects;

use Domain\ValueObjects\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_should_accept_valid_email(): void
    {
        $email = new Email('test@example.com');
        $this->assertEquals('test@example.com', $email->getValue());
    }

    public function test_should_throw_exception_for_invalid_email(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('E-mail inválido.');
        new Email('invalid-email');
    }

    public function test_should_identify_corporate_email(): void
    {
        $corporate = new Email('bruno@empresa.com.br');
        $personal = new Email('bruno@gmail.com');

        $this->assertTrue($corporate->isCorporate());
        $this->assertFalse($personal->isCorporate());
    }

    public function test_should_identify_brazilian_email(): void
    {
        $brazilian = new Email('test@mail.com.br');
        $international = new Email('test@google.com');

        $this->assertTrue($brazilian->isBrazilian());
        $this->assertFalse($international->isBrazilian());
    }
}
