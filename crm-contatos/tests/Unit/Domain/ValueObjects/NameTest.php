<?php

namespace Tests\Unit\Domain\ValueObjects;

use Domain\ValueObjects\Name;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function test_should_accept_valid_name(): void
    {
        $name = new Name('Bruno');
        $this->assertEquals('Bruno', $name->getValue());
    }

    public function test_should_throw_exception_for_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('O nome não pode ser vazio.');
        new Name('   ');
    }

    public function test_should_identify_full_name(): void
    {
        $fullName = new Name('Bruno Melo');
        $singleName = new Name('Bruno');

        $this->assertTrue($fullName->isFullName());
        $this->assertFalse($singleName->isFullName());
    }
}
