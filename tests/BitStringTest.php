<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString\Tests;

use Guillaumetissier\BitString\BitString;
use PHPUnit\Framework\TestCase;

class BitStringTest extends TestCase
{
    public function testFromString(): void
    {
        $bits = BitString::fromString('10110101');
        $this->assertEquals('10110101', $bits->toString());
    }

    public function testSetMutatesInstance(): void
    {
        $bits = BitString::fromString('1011');
        $result = $bits->set(1, 1);

        // Returns same instance
        $this->assertSame($bits, $result);
        // Instance is mutated
        $this->assertEquals('1111', $bits->toString());
    }

    public function testFlipMutatesInstance(): void
    {
        $bits = BitString::fromString('1011');
        $bits->flip(1);

        $this->assertEquals('1111', $bits->toString());
    }

    public function testAndMutatesInstance(): void
    {
        $a = BitString::fromString('1100');
        $b = BitString::fromString('1010');

        $result = $a->and($b);

        // Returns same instance
        $this->assertSame($a, $result);
        // Instance is mutated
        $this->assertEquals('1000', $a->toString());
    }

    public function testOrMutatesInstance(): void
    {
        $bits = BitString::fromString('1100');
        $bits->or(BitString::fromString('1010'));

        $this->assertEquals('1110', $bits->toString());
    }

    public function testXorMutatesInstance(): void
    {
        $bits = BitString::fromString('1100');
        $bits->xor(BitString::fromString('1010'));

        $this->assertEquals('0110', $bits->toString());
    }

    public function testNotMutatesInstance(): void
    {
        $bits = BitString::fromString('1100');
        $bits->not();

        $this->assertEquals('0011', $bits->toString());
    }

    public function testShiftLeftMutatesInstance(): void
    {
        $bits = BitString::fromString('10110101');
        $bits->shiftLeft(2);

        $this->assertEquals('11010100', $bits->toString());
    }

    public function testShiftRightMutatesInstance(): void
    {
        $bits = BitString::fromString('10110101');
        $bits->shiftRight(2);

        $this->assertEquals('00101101', $bits->toString());
    }

    public function testRotateLeftMutatesInstance(): void
    {
        $bits = BitString::fromString('10110101');
        $bits->rotateLeft(2);

        $this->assertEquals('11010110', $bits->toString());
    }

    public function testRotateRightMutatesInstance(): void
    {
        $bits = BitString::fromString('10110101');
        $bits->rotateRight(2);

        $this->assertEquals('01101101', $bits->toString());
    }

    public function testPrependMutatesInstance(): void
    {
        $bits = BitString::fromString('0101');
        $prefix = BitString::fromString('11');
        $bits->prepend($prefix);

        $this->assertEquals('110101', $bits->toString());
    }

    public function testAppendMutatesInstance(): void
    {
        $bits = BitString::fromString('1011');
        $suffix = BitString::fromString('00');
        $bits->append($suffix);

        $this->assertEquals('101100', $bits->toString());
    }

    public function testChainedOperations(): void
    {
        $bits = BitString::fromString('11');
        $bits
            ->prepend(BitString::fromString('00'))
            ->append(BitString::fromString('11'))
            ->not();

        $this->assertEquals('110000', $bits->toString());
    }

    public function testMutabilityVsImmutability(): void
    {
        // Mutable
        $mutable = BitString::fromString('1100');
        $mutable->not();
        $this->assertEquals('0011', $mutable->toString());

        // Immutable would create new instance
        // This test shows the difference in behavior
    }
}
