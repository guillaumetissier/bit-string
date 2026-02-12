<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString\Tests;

use Guillaumetissier\BitString\BitStringImmutable;
use Guillaumetissier\BitString\Converter\BinaryConverter;
use Guillaumetissier\BitString\Converter\CodewordConverter;
use Guillaumetissier\BitString\Converter\DecimalConverter;
use Guillaumetissier\BitString\Converter\HexConverter;
use PHPUnit\Framework\TestCase;

class BitStringImmutableTest extends TestCase
{
    public function testFromString(): void
    {
        $bits = BitStringImmutable::fromString('10110101');
        $this->assertEquals('10110101', $bits->toString());
    }

    public function testFromStringThrowsOnInvalidInput(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        BitStringImmutable::fromString('102');
    }

    public function testZeros(): void
    {
        $bits = BitStringImmutable::zeros(8);
        $this->assertEquals('00000000', $bits->toString());
    }

    public function testOnes(): void
    {
        $bits = BitStringImmutable::ones(8);
        $this->assertEquals('11111111', $bits->toString());
    }

    public function testBinaryConverter(): void
    {
        $converter = new BinaryConverter();
        $bits = $converter->toBitString('10110101');
        $this->assertEquals('10110101', $converter->fromBitString($bits));
    }

    public function testBinaryConverterImmutable(): void
    {
        $converter = new BinaryConverter();
        $bits = $converter->toBitStringImmutable('10110101');
        $this->assertInstanceOf(BitStringImmutable::class, $bits);
        $this->assertEquals('10110101', $converter->fromBitString($bits));
    }

    public function testHexConverter(): void
    {
        $converter = new HexConverter();
        $bits = $converter->toBitString('B5');
        $this->assertEquals('B5', $converter->fromBitString($bits));
    }

    public function testHexConverterImmutable(): void
    {
        $converter = new HexConverter();
        $bits = $converter->toBitStringImmutable('B5');
        $this->assertInstanceOf(BitStringImmutable::class, $bits);
        $this->assertEquals('B5', $converter->fromBitString($bits));
    }

    public function testHexConverterWithPrefix(): void
    {
        $converter = new HexConverter();
        $bits = $converter->toBitString('0xB5');

        $converter->withPrefix(true);
        $this->assertEquals('0xB5', $converter->fromBitString($bits));
    }

    public function testDecimalConverter(): void
    {
        $converter = new DecimalConverter();
        $bits = $converter->toBitString(181);
        $this->assertEquals(181, $converter->fromBitString($bits));
    }

    public function testDecimalConverterWithWidth(): void
    {
        $converter = new DecimalConverter();
        $converter->withWidth(8);
        $bits = $converter->toBitString(5);
        $this->assertEquals('00000101', $bits->toString());
    }

    public function testCodewordConverter(): void
    {
        $converter = new CodewordConverter();
        $converter->withWordLength(4);
        $bits = $converter->toBitString(['1011', '0101']);
        $this->assertEquals(['1011', '0101'], $converter->fromBitString($bits));
    }

    public function testCodewordConverterWithPadding(): void
    {
        $converter = new CodewordConverter();
        $converter->withWordLength(4)->withPadding(true);
        $bits = BitStringImmutable::fromString('101101');
        $codewords = $converter->fromBitString($bits);
        $this->assertEquals(['1011', '0100'], $codewords);
    }

    public function testGet(): void
    {
        $bits = BitStringImmutable::fromString('1011');
        $this->assertEquals(1, $bits->get(0));
        $this->assertEquals(0, $bits->get(1));
        $this->assertEquals(1, $bits->get(2));
        $this->assertEquals(1, $bits->get(3));
    }

    public function testGetThrowsOnOutOfBounds(): void
    {
        $bits = BitStringImmutable::fromString('1011');
        $this->expectException(\OutOfBoundsException::class);
        $bits->get(4);
    }

    public function testSetCreatesNewInstance(): void
    {
        $bits = BitStringImmutable::fromString('1011');
        $newBits = $bits->set(1, 1);

        $this->assertEquals('1111', $newBits->toString());
        $this->assertEquals('1011', $bits->toString());
    }

    public function testSetThrowsOnInvalidValue(): void
    {
        $bits = BitStringImmutable::fromString('1011');
        $this->expectException(\InvalidArgumentException::class);
        $bits->set(0, 2);
    }

    public function testFlip(): void
    {
        $bits = BitStringImmutable::fromString('1011');
        $newBits = $bits->flip(1);

        $this->assertEquals('1111', $newBits->toString());
        $this->assertEquals('1011', $bits->toString());
    }

    public function testLength(): void
    {
        $bits = BitStringImmutable::fromString('10110101');

        $this->assertEquals(8, $bits->length());
    }

    public function testBitCount(): void
    {
        $bits = BitStringImmutable::fromString('10110101');

        $this->assertEquals(8, $bits->bitCount());
        $this->assertEquals($bits->length(), $bits->bitCount());
    }

    public function testAnd(): void
    {
        $a = BitStringImmutable::fromString('1100');
        $b = BitStringImmutable::fromString('1010');
        $result = $a->and($b);

        $this->assertEquals('1000', $result->toString());
        $this->assertEquals('1100', $a->toString());
        $this->assertEquals('1010', $b->toString());
    }

    public function testOr(): void
    {
        $a = BitStringImmutable::fromString('1100');
        $b = BitStringImmutable::fromString('1010');
        $result = $a->or($b);

        $this->assertEquals('1110', $result->toString());
    }

    public function testXor(): void
    {
        $a = BitStringImmutable::fromString('1100');
        $b = BitStringImmutable::fromString('1010');
        $result = $a->xor($b);

        $this->assertEquals('0110', $result->toString());
    }

    public function testNot(): void
    {
        $bits = BitStringImmutable::fromString('1100');
        $result = $bits->not();

        $this->assertEquals('0011', $result->toString());
        $this->assertEquals('1100', $bits->toString());
    }

    public function testShiftLeft(): void
    {
        $bits = BitStringImmutable::fromString('10110101');
        $result = $bits->shiftLeft(2);
        $this->assertEquals('11010100', $result->toString());
    }

    public function testShiftRight(): void
    {
        $bits = BitStringImmutable::fromString('10110101');
        $result = $bits->shiftRight(2);
        $this->assertEquals('00101101', $result->toString());
    }

    public function testRotateLeft(): void
    {
        $bits = BitStringImmutable::fromString('10110101');
        $result = $bits->rotateLeft(2);
        $this->assertEquals('11010110', $result->toString());
    }

    public function testRotateRight(): void
    {
        $bits = BitStringImmutable::fromString('10110101');
        $result = $bits->rotateRight(2);
        $this->assertEquals('01101101', $result->toString());
    }

    public function testPopCount(): void
    {
        $bits = BitStringImmutable::fromString('10110101');
        $this->assertEquals(5, $bits->popCount());
    }

    public function testPrepend(): void
    {
        $bits = BitStringImmutable::fromString('0101');
        $prefix = BitStringImmutable::fromString('11');
        $result = $bits->prepend($prefix);

        $this->assertEquals('110101', $result->toString());
        $this->assertEquals('0101', $bits->toString());
        $this->assertEquals('11', $prefix->toString());
    }

    public function testPrependString(): void
    {
        $bits = BitStringImmutable::fromString('0101');
        $result = $bits->prepend('11');

        $this->assertEquals('110101', $result->toString());
        $this->assertEquals('0101', $bits->toString());
    }

    public function testPrependStringThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $bits = BitStringImmutable::fromString('0101');
        $bits->prepend('AB');
    }

    public function testAppend(): void
    {
        $bits = BitStringImmutable::fromString('1011');
        $suffix = BitStringImmutable::fromString('00');
        $result = $bits->append($suffix);

        $this->assertEquals('101100', $result->toString());
        $this->assertEquals('1011', $bits->toString());
        $this->assertEquals('00', $suffix->toString());
    }

    public function testAppendString(): void
    {
        $bits = BitStringImmutable::fromString('1011');
        $result = $bits->append('00');

        $this->assertEquals('101100', $result->toString());
        $this->assertEquals('1011', $bits->toString());
    }

    public function testAppendStringThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $bits = BitStringImmutable::fromString('0101');
        $bits->append('AB');
    }

    public function testChainedOperations(): void
    {
        $bits = BitStringImmutable::fromString('11');
        $result = $bits
            ->prepend(BitStringImmutable::fromString('00'))
            ->append(BitStringImmutable::fromString('11'))
            ->not();

        $this->assertEquals('110000', $result->toString());
        // Original unchanged
        $this->assertEquals('11', $bits->toString());
    }

    public function testEquals(): void
    {
        $a = BitStringImmutable::fromString('1011');
        $b = BitStringImmutable::fromString('1011');
        $c = BitStringImmutable::fromString('1010');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }

    public function testToString(): void
    {
        $bits = BitStringImmutable::fromString('1011');
        $this->assertEquals('1011', (string) $bits);
    }

    public function testBitwiseOperationsThrowOnDifferentLengths(): void
    {
        $a = BitStringImmutable::fromString('1100');
        $b = BitStringImmutable::fromString('10');

        $this->expectException(\InvalidArgumentException::class);
        $a->and($b);
    }

    public function testExtractImmutable(): void
    {
        $bits = BitStringImmutable::fromString('10110101');
        $result = $bits->extract(2, 4);

        $this->assertInstanceOf(BitStringImmutable::class, $result);
        $this->assertEquals('1101', $result->toString());
    }

    public function testSliceImmutable(): void
    {
        $bits = BitStringImmutable::fromString('10110101');
        $result = $bits->slice(2, 6);

        $this->assertInstanceOf(BitStringImmutable::class, $result);
        $this->assertEquals('1101', $result->toString());
    }

    public function testFirstImmutable(): void
    {
        $bits = BitStringImmutable::fromString('10110101');
        $result = $bits->first(3);

        $this->assertInstanceOf(BitStringImmutable::class, $result);
        $this->assertEquals('101', $result->toString());
    }

    public function testLastImmutable(): void
    {
        $bits = BitStringImmutable::fromString('10110101');
        $result = $bits->last(3);

        $this->assertInstanceOf(BitStringImmutable::class, $result);
        $this->assertEquals('101', $result->toString());
    }

    public function testCodewordImmutable(): void
    {
        $bits = BitStringImmutable::fromString('10110101');
        $result = $bits->codeword(0, 4);

        $this->assertInstanceOf(BitStringImmutable::class, $result);
        $this->assertEquals('1011', $result->toString());
    }

    /**
     * @dataProvider dataPad
     */
    public function testPad(BitStringImmutable $bitString, int $length, bool $prepend, string $expected): void
    {
        $paddedBitString = $bitString->pad($length, $prepend);
        $this->assertInstanceOf(BitStringImmutable::class, $paddedBitString);
        $this->assertNotSame($bitString, $paddedBitString);
        $this->assertEquals($expected, $paddedBitString->toString());
    }

    public static function dataPad(): \Generator
    {
        yield [BitStringImmutable::fromString('1010101'), -1, true, '1010101'];
        yield [BitStringImmutable::fromString('1010101'), 6, true, '1010101'];
        yield [BitStringImmutable::ones(7), 10, true, '0001111111'];
        yield [BitStringImmutable::ones(8), 10, false, '1111111100'];
    }
}
