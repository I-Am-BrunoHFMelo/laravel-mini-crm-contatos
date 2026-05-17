<?php

namespace Tests\Unit\Domain\ValueObjects;

use Domain\ValueObjects\Phone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{
    public function test_should_accept_valid_phone(): void
    {
        $phone = new Phone('(11) 99999-9999');
        $this->assertEquals('11999999999', $phone->getValue());
    }

    public function test_should_throw_exception_for_invalid_phone_length(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Telefone inválido. Deve conter DDD e número.');
        new Phone('1234567');
    }

    public function test_should_identify_phone_from_sao_paulo(): void
    {
        $sp = new Phone('11988887777');
        $rj = new Phone('21988887777');

        $this->assertTrue($sp->isFromSaoPaulo());
        $this->assertFalse($rj->isFromSaoPaulo());
    }
}
