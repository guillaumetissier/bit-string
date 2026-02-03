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
        $mutable = BitString::fromString('1100');
        $mutable->not();
        $this->assertEquals('0011', $mutable->toString());
    }

    public function testExtractMutable(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->extract(2, 4);

        $this->assertInstanceOf(BitString::class, $result);
        $this->assertEquals('1101', $result->toString());
    }

    public function testExtractFromStart(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->extract(0, 3);

        $this->assertEquals('101', $result->toString());
    }

    public function testExtractToEnd(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->extract(5, 3);

        $this->assertEquals('101', $result->toString());
    }

    public function testExtractSingleBit(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->extract(3, 1);

        $this->assertEquals('1', $result->toString());
    }

    public function testExtractFullLength(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->extract(0, 8);

        $this->assertEquals('10110101', $result->toString());
    }

    public function testExtractThrowsOnNegativePosition(): void
    {
        $bits = BitString::fromString('10110101');

        $this->expectException(\OutOfBoundsException::class);
        $bits->extract(-1, 2);
    }

    public function testExtractThrowsOnPositionOutOfBounds(): void
    {
        $bits = BitString::fromString('10110101');

        $this->expectException(\OutOfBoundsException::class);
        $bits->extract(8, 1);
    }

    public function testExtractThrowsOnExceedingLength(): void
    {
        $bits = BitString::fromString('10110101');

        $this->expectException(\OutOfBoundsException::class);
        $bits->extract(6, 3);
    }

    public function testExtractThrowsOnZeroLength(): void
    {
        $bits = BitString::fromString('10110101');

        $this->expectException(\InvalidArgumentException::class);
        $bits->extract(0, 0);
    }

    public function testSliceMutable(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->slice(2, 6);

        $this->assertInstanceOf(BitString::class, $result);
        $this->assertEquals('1101', $result->toString());
    }

    public function testSliceFromStart(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->slice(0, 3);

        $this->assertEquals('101', $result->toString());
    }

    public function testSliceToEnd(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->slice(5, 8);

        $this->assertEquals('101', $result->toString());
    }

    public function testSliceFullLength(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->slice(0, 8);

        $this->assertEquals('10110101', $result->toString());
    }

    public function testSliceThrowsOnStartGreaterThanEnd(): void
    {
        $bits = BitString::fromString('10110101');

        $this->expectException(\InvalidArgumentException::class);
        $bits->slice(5, 3);
    }

    public function testSliceThrowsOnEqualStartAndEnd(): void
    {
        $bits = BitString::fromString('10110101');

        $this->expectException(\InvalidArgumentException::class);
        $bits->slice(3, 3);
    }

    public function testSliceThrowsOnStartOutOfBounds(): void
    {
        $bits = BitString::fromString('10110101');

        $this->expectException(\OutOfBoundsException::class);
        $bits->slice(8, 10);
    }

    public function testSliceThrowsOnEndOutOfBounds(): void
    {
        $bits = BitString::fromString('10110101');

        $this->expectException(\OutOfBoundsException::class);
        $bits->slice(5, 9);
    }

    public function testFirstMutable(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->first(3);

        $this->assertInstanceOf(BitString::class, $result);
        $this->assertEquals('101', $result->toString());
    }

    public function testFirstSingleBit(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->first(1);

        $this->assertEquals('1', $result->toString());
    }

    public function testFirstFullLength(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->first(8);

        $this->assertEquals('10110101', $result->toString());
    }

    public function testFirstThrowsOnZeroLength(): void
    {
        $bits = BitString::fromString('10110101');

        $this->expectException(\InvalidArgumentException::class);
        $bits->first(0);
    }

    public function testFirstThrowsOnExceedingLength(): void
    {
        $bits = BitString::fromString('10110101');

        $this->expectException(\OutOfBoundsException::class);
        $bits->first(9);
    }

    public function testLastMutable(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->last(3);

        $this->assertInstanceOf(BitString::class, $result);
        $this->assertEquals('101', $result->toString());
    }

    public function testLastSingleBit(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->last(1);

        $this->assertEquals('1', $result->toString());
    }

    public function testLastFullLength(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->last(8);

        $this->assertEquals('10110101', $result->toString());
    }

    public function testLastThrowsOnZeroLength(): void
    {
        $bits = BitString::fromString('10110101');

        $this->expectException(\InvalidArgumentException::class);
        $bits->last(0);
    }

    public function testLastThrowsOnExceedingLength(): void
    {
        $bits = BitString::fromString('10110101');

        $this->expectException(\OutOfBoundsException::class);
        $bits->last(9);
    }

    public function testCodewordMutable(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->codeword(0, 4);

        $this->assertInstanceOf(BitString::class, $result);
        $this->assertEquals('1011', $result->toString());
    }

    public function testCodewordFirst(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->codeword(0, 4);

        $this->assertEquals('1011', $result->toString());
    }

    public function testCodewordSecond(): void
    {
        $bits = BitString::fromString('10110101');
        $result = $bits->codeword(1, 4);

        $this->assertEquals('0101', $result->toString());
    }

    public function testCodewordWithWordLength8(): void
    {
        $bits = BitString::fromString('1011010111001100');
        $result = $bits->codeword(1, 8);

        $this->assertEquals('11001100', $result->toString());
    }

    public function testCodewordWithWordLength2(): void
    {
        $bits = BitString::fromString('10110101');

        $this->assertEquals('10', $bits->codeword(0, 2)->toString());
        $this->assertEquals('11', $bits->codeword(1, 2)->toString());
        $this->assertEquals('01', $bits->codeword(2, 2)->toString());
        $this->assertEquals('01', $bits->codeword(3, 2)->toString());
    }

    public function testCodewordIncompleteLastWord(): void
    {
        $bits = BitString::fromString('101101'); // 6 bits
        $result = $bits->codeword(1, 4);        // position 4, only 2 bits left

        $this->assertEquals('01', $result->toString());
    }

    public function testCodewordThrowsOnZeroWordLength(): void
    {
        $bits = BitString::fromString('10110101');

        $this->expectException(\InvalidArgumentException::class);
        $bits->codeword(0, 0);
    }

    public function testCodewordThrowsOnIndexOutOfBounds(): void
    {
        $bits = BitString::fromString('10110101');

        $this->expectException(\OutOfBoundsException::class);
        $bits->codeword(2, 4); // position 8, out of bounds
    }

    public function testFirstAndLastCombined(): void
    {
        $bits = BitString::fromString('10110101');

        // first(3) + middle + last(3) = original
        $first = $bits->first(3);
        $last = $bits->last(3);

        $this->assertEquals('101', $first->toString());
        $this->assertEquals('101', $last->toString());
    }

    public function testSliceEqualsExtract(): void
    {
        $bits = BitString::fromString('10110101');

        // slice(2, 6) === extract(2, 4)
        $sliced = $bits->slice(2, 6);
        $extracted = $bits->extract(2, 4);

        $this->assertTrue($sliced->equals($extracted));
    }

    public function testFirstEqualsSliceFromZero(): void
    {
        $bits = BitString::fromString('10110101');

        $first = $bits->first(3);
        $sliced = $bits->slice(0, 3);

        $this->assertTrue($first->equals($sliced));
    }

    public function testLastEqualsSliceToEnd(): void
    {
        $bits = BitString::fromString('10110101');

        $last = $bits->last(3);
        $sliced = $bits->slice(5, 8);

        $this->assertTrue($last->equals($sliced));
    }

    public function testCodewordEqualsExtract(): void
    {
        $bits = BitString::fromString('10110101');

        // codeword(1, 4) === extract(4, 4)
        $cw = $bits->codeword(1, 4);
        $extracted = $bits->extract(4, 4);

        $this->assertTrue($cw->equals($extracted));
    }
}
