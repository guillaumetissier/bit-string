<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString\Tests\Converter;

use Guillaumetissier\BitString\BitString;
use Guillaumetissier\BitString\BitStringImmutable;
use Guillaumetissier\BitString\Converter\BinaryConverter;
use PHPUnit\Framework\TestCase;

class BinaryConverterTest extends TestCase
{
    public function testToBitString(): void
    {
        $converter = new BinaryConverter();
        $bits = $converter->toBitString('10110101');

        $this->assertInstanceOf(BitString::class, $bits);
        $this->assertEquals('10110101', $bits->toString());
    }

    public function testToBitStringImmutable(): void
    {
        $converter = new BinaryConverter();
        $bits = $converter->toBitStringImmutable('10110101');

        $this->assertInstanceOf(BitStringImmutable::class, $bits);
        $this->assertEquals('10110101', $bits->toString());
    }

    public function testFromBitString(): void
    {
        $converter = new BinaryConverter();
        $bits = BitString::fromString('10110101');

        $this->assertEquals('10110101', $converter->fromBitString($bits));
    }

    public function testFromBitStringImmutable(): void
    {
        $converter = new BinaryConverter();
        $bits = BitStringImmutable::fromString('10110101');

        $this->assertEquals('10110101', $converter->fromBitString($bits));
    }

    public function testThrowsOnInvalidType(): void
    {
        $converter = new BinaryConverter();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a string');
        $converter->toBitString(123);
    }

    public function testThrowsOnInvalidBinary(): void
    {
        $converter = new BinaryConverter();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Binary string must contain only 0 and 1');
        $converter->toBitString('102');
    }

    public function testRoundTrip(): void
    {
        $converter = new BinaryConverter();
        $original = '10110101';

        $bits = $converter->toBitString($original);
        $result = $converter->fromBitString($bits);

        $this->assertEquals($original, $result);
    }

    public function testRoundTripImmutable(): void
    {
        $converter = new BinaryConverter();
        $original = '10110101';

        $bits = $converter->toBitStringImmutable($original);
        $result = $converter->fromBitString($bits);

        $this->assertEquals($original, $result);
    }

    public function testSingleBit(): void
    {
        $converter = new BinaryConverter();

        $bits = $converter->toBitString('1');
        $this->assertEquals('1', $converter->fromBitString($bits));

        $bits = $converter->toBitString('0');
        $this->assertEquals('0', $converter->fromBitString($bits));
    }

    public function testLongBitString(): void
    {
        $converter = new BinaryConverter();
        $long = str_repeat('10', 100); // 200 bits

        $bits = $converter->toBitString($long);
        $this->assertEquals($long, $converter->fromBitString($bits));
    }
}
