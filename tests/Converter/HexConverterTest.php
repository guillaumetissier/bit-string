<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString\Tests\Converter;

use Guillaumetissier\BitString\BitString;
use Guillaumetissier\BitString\BitStringImmutable;
use Guillaumetissier\BitString\Converter\HexConverter;
use PHPUnit\Framework\TestCase;

class HexConverterTest extends TestCase
{
    public function testToBitString(): void
    {
        $converter = new HexConverter();
        $bits = $converter->toBitString('B5');

        $this->assertInstanceOf(BitString::class, $bits);
        $this->assertEquals('10110101', $bits->toString());
    }

    public function testToBitStringImmutable(): void
    {
        $converter = new HexConverter();
        $bits = $converter->toBitStringImmutable('B5');

        $this->assertInstanceOf(BitStringImmutable::class, $bits);
        $this->assertEquals('10110101', $bits->toString());
    }

    public function testFromBitString(): void
    {
        $converter = new HexConverter();
        $bits = BitString::fromString('10110101');

        $this->assertEquals('B5', $converter->fromBitString($bits));
    }

    public function testWithPrefix(): void
    {
        $converter = new HexConverter();
        $converter->withPrefix(true);
        $bits = BitString::fromString('10110101');

        $this->assertEquals('0xB5', $converter->fromBitString($bits));
    }

    public function testWithoutPrefix(): void
    {
        $converter = new HexConverter();
        $converter->withPrefix(false);
        $bits = BitString::fromString('10110101');

        $this->assertEquals('B5', $converter->fromBitString($bits));
    }

    public function testAccepts0xPrefix(): void
    {
        $converter = new HexConverter();
        $bits = $converter->toBitString('0xB5');

        $this->assertEquals('10110101', $bits->toString());
    }

    public function testLowercaseHex(): void
    {
        $converter = new HexConverter();
        $bits = $converter->toBitString('b5');

        $this->assertEquals('10110101', $bits->toString());
    }

    public function testSingleDigit(): void
    {
        $converter = new HexConverter();
        $bits = $converter->toBitString('F');

        $this->assertEquals('1111', $bits->toString());
    }

    public function testPadding(): void
    {
        $converter = new HexConverter();
        $bits = BitString::fromString('101'); // 3 bits

        // Should pad to 4 bits (0101) = 5 in hex
        $this->assertEquals('5', $converter->fromBitString($bits));
    }

    public function testPaddingMultipleDigits(): void
    {
        $converter = new HexConverter();
        $bits = BitString::fromString('11111'); // 5 bits

        // Should pad to 8 bits (00011111) = 1F in hex
        $this->assertEquals('1F', $converter->fromBitString($bits));
    }

    public function testThrowsOnInvalidType(): void
    {
        $converter = new HexConverter();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a string');
        $converter->toBitString(123);
    }

    public function testThrowsOnInvalidHex(): void
    {
        $converter = new HexConverter();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid hexadecimal string');
        $converter->toBitString('XYZ');
    }

    public function testEmptyString(): void
    {
        $converter = new HexConverter();

        $this->expectException(\InvalidArgumentException::class);
        $converter->toBitString('');
    }

    public function testRoundTrip(): void
    {
        $converter = new HexConverter();
        $original = 'DEADBEEF';

        $bits = $converter->toBitString($original);
        $result = $converter->fromBitString($bits);

        $this->assertEquals($original, $result);
    }

    public function testRoundTripWithPrefix(): void
    {
        $converter = new HexConverter();
        $converter->withPrefix(true);

        $bits = $converter->toBitString('0xB5');
        $result = $converter->fromBitString($bits);

        $this->assertEquals('0xB5', $result);
    }

    public function testAllHexDigits(): void
    {
        $converter = new HexConverter();

        $hexDigits = '123456789ABCDEF';
        $bits = $converter->toBitString($hexDigits);
        $result = $converter->fromBitString($bits);

        $this->assertEquals($hexDigits, $result);
    }

    public function testZero(): void
    {
        $converter = new HexConverter();
        $bits = $converter->toBitString('0');

        $this->assertEquals('0000', $bits->toString());
        $this->assertEquals('0', $converter->fromBitString($bits));
    }
}
