<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString\Tests\Converter;

use Guillaumetissier\BitString\BitString;
use Guillaumetissier\BitString\BitStringImmutable;
use Guillaumetissier\BitString\Converter\DecimalConverter;
use PHPUnit\Framework\TestCase;

class DecimalConverterTest extends TestCase
{
    public function testToBitString(): void
    {
        $converter = new DecimalConverter();
        $bits = $converter->toBitString(181);

        $this->assertInstanceOf(BitString::class, $bits);
        $this->assertEquals('10110101', $bits->toString());
    }

    public function testToBitStringImmutable(): void
    {
        $converter = new DecimalConverter();
        $bits = $converter->toBitStringImmutable(181);

        $this->assertInstanceOf(BitStringImmutable::class, $bits);
        $this->assertEquals('10110101', $bits->toString());
    }

    public function testFromBitString(): void
    {
        $converter = new DecimalConverter();
        $bits = BitString::fromString('10110101');

        $this->assertEquals(181, $converter->fromBitString($bits));
    }

    public function testWithWidth(): void
    {
        $converter = new DecimalConverter();
        $converter->withWidth(8);
        $bits = $converter->toBitString(5);

        $this->assertEquals('00000101', $bits->toString());
    }

    public function testWithWidthExactMatch(): void
    {
        $converter = new DecimalConverter();
        $converter->withWidth(8);
        $bits = $converter->toBitString(255);

        $this->assertEquals('11111111', $bits->toString());
    }

    public function testWithoutWidth(): void
    {
        $converter = new DecimalConverter();
        $converter->withWidth(null);
        $bits = $converter->toBitString(5);

        $this->assertEquals('101', $bits->toString());
    }

    public function testZero(): void
    {
        $converter = new DecimalConverter();
        $bits = $converter->toBitString(0);

        $this->assertEquals('0', $bits->toString());
    }

    public function testZeroWithWidth(): void
    {
        $converter = new DecimalConverter();
        $converter->withWidth(4);
        $bits = $converter->toBitString(0);

        $this->assertEquals('0000', $bits->toString());
    }

    public function testOne(): void
    {
        $converter = new DecimalConverter();
        $bits = $converter->toBitString(1);

        $this->assertEquals('1', $bits->toString());
    }

    public function testPowerOfTwo(): void
    {
        $converter = new DecimalConverter();

        $bits = $converter->toBitString(16);
        $this->assertEquals('10000', $bits->toString());

        $bits = $converter->toBitString(256);
        $this->assertEquals('100000000', $bits->toString());
    }

    public function testLargeNumber(): void
    {
        $converter = new DecimalConverter();
        $bits = $converter->toBitString(65535); // 0xFFFF

        $this->assertEquals('1111111111111111', $bits->toString());
    }

    public function testThrowsOnInvalidType(): void
    {
        $converter = new DecimalConverter();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer');
        $converter->toBitString('123');
    }

    public function testThrowsOnNegative(): void
    {
        $converter = new DecimalConverter();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Decimal number must be non-negative');
        $converter->toBitString(-1);
    }

    public function testThrowsOnInsufficientWidth(): void
    {
        $converter = new DecimalConverter();
        $converter->withWidth(4);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Width is too small for the decimal value');
        $converter->toBitString(255);
    }

    public function testThrowsOnInsufficientWidthByOne(): void
    {
        $converter = new DecimalConverter();
        $converter->withWidth(7);

        $this->expectException(\InvalidArgumentException::class);
        $converter->toBitString(255); // Needs 8 bits
    }

    public function testRoundTrip(): void
    {
        $converter = new DecimalConverter();
        $original = 181;

        $bits = $converter->toBitString($original);
        $result = $converter->fromBitString($bits);

        $this->assertEquals($original, $result);
    }

    public function testRoundTripWithWidth(): void
    {
        $converter = new DecimalConverter();
        $converter->withWidth(16);
        $original = 181;

        $bits = $converter->toBitString($original);
        $result = $converter->fromBitString($bits);

        $this->assertEquals($original, $result);
    }

    public function testRoundTripZero(): void
    {
        $converter = new DecimalConverter();

        $bits = $converter->toBitString(0);
        $result = $converter->fromBitString($bits);

        $this->assertEquals(0, $result);
    }

    public function testMultipleWidthChanges(): void
    {
        $converter = new DecimalConverter();

        $converter->withWidth(4);
        $bits = $converter->toBitString(5);
        $this->assertEquals('0101', $bits->toString());

        $converter->withWidth(8);
        $bits = $converter->toBitString(5);
        $this->assertEquals('00000101', $bits->toString());

        $converter->withWidth(null);
        $bits = $converter->toBitString(5);
        $this->assertEquals('101', $bits->toString());
    }
}
