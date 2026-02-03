<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString\Tests\Converter;

use Guillaumetissier\BitString\BitString;
use Guillaumetissier\BitString\BitStringImmutable;
use Guillaumetissier\BitString\Converter\CodewordConverter;
use PHPUnit\Framework\TestCase;

class CodewordConverterTest extends TestCase
{
    public function testToBitString(): void
    {
        $converter = new CodewordConverter();
        $bits = $converter->toBitString(['1011', '0101']);

        $this->assertInstanceOf(BitString::class, $bits);
        $this->assertEquals('10110101', $bits->toString());
    }

    public function testToBitStringImmutable(): void
    {
        $converter = new CodewordConverter();
        $bits = $converter->toBitStringImmutable(['1011', '0101']);

        $this->assertInstanceOf(BitStringImmutable::class, $bits);
        $this->assertEquals('10110101', $bits->toString());
    }

    public function testFromBitString(): void
    {
        $converter = new CodewordConverter();
        $converter->withWordLength(4);
        $bits = BitString::fromString('10110101');

        $this->assertEquals(['1011', '0101'], $converter->fromBitString($bits));
    }

    public function testWithPaddingEnabled(): void
    {
        $converter = new CodewordConverter();
        $converter->withWordLength(4)->withPadding(true);
        $bits = BitString::fromString('101101');

        $codewords = $converter->fromBitString($bits);
        $this->assertEquals(['1011', '0100'], $codewords);
    }

    public function testWithPaddingDisabled(): void
    {
        $converter = new CodewordConverter();
        $converter->withWordLength(4)->withPadding(false);
        $bits = BitString::fromString('101101');

        $codewords = $converter->fromBitString($bits);
        $this->assertEquals(['1011', '01'], $codewords);
    }

    public function testWordLength2(): void
    {
        $converter = new CodewordConverter();
        $converter->withWordLength(2);
        $bits = BitString::fromString('10110101');

        $this->assertEquals(['10', '11', '01', '01'], $converter->fromBitString($bits));
    }

    public function testWordLength8(): void
    {
        $converter = new CodewordConverter();
        $converter->withWordLength(8);
        $bits = BitString::fromString('10110101');

        $this->assertEquals(['10110101'], $converter->fromBitString($bits));
    }

    public function testWordLength1(): void
    {
        $converter = new CodewordConverter();
        $converter->withWordLength(1);
        $bits = BitString::fromString('1011');

        $this->assertEquals(['1', '0', '1', '1'], $converter->fromBitString($bits));
    }

    public function testSingleCodeword(): void
    {
        $converter = new CodewordConverter();
        $bits = $converter->toBitString(['10110101']);

        $this->assertEquals('10110101', $bits->toString());
    }

    public function testMultipleCodewords(): void
    {
        $converter = new CodewordConverter();
        $bits = $converter->toBitString(['11', '00', '11', '00']);

        $this->assertEquals('11001100', $bits->toString());
    }

    public function testVariableLengthCodewords(): void
    {
        $converter = new CodewordConverter();
        $bits = $converter->toBitString(['1', '00', '111', '0000']);

        $this->assertEquals('1001110000', $bits->toString());
    }

    public function testPaddingWithExactMatch(): void
    {
        $converter = new CodewordConverter();
        $converter->withWordLength(4)->withPadding(true);
        $bits = BitString::fromString('10110101'); // Exactly 8 bits

        $codewords = $converter->fromBitString($bits);
        $this->assertEquals(['1011', '0101'], $codewords);
    }

    public function testThrowsOnInvalidType(): void
    {
        $converter = new CodewordConverter();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an array');
        $converter->toBitString('not-an-array');
    }

    public function testThrowsOnInvalidWordLength(): void
    {
        $converter = new CodewordConverter();
        $converter->withWordLength(0);
        $bits = BitString::fromString('1011');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Word length must be at least 1');
        $converter->fromBitString($bits);
    }

    public function testRoundTrip(): void
    {
        $converter = new CodewordConverter();
        $converter->withWordLength(4)->withPadding(true);
        $original = ['1011', '0101'];

        $bits = $converter->toBitString($original);
        $result = $converter->fromBitString($bits);

        $this->assertEquals($original, $result);
    }

    public function testRoundTripWithoutPadding(): void
    {
        $converter = new CodewordConverter();
        $converter->withWordLength(4)->withPadding(false);
        $original = ['1011', '0101'];

        $bits = $converter->toBitString($original);
        $result = $converter->fromBitString($bits);

        $this->assertEquals($original, $result);
    }

    public function testRoundTripWithPadding(): void
    {
        $converter = new CodewordConverter();
        $converter->withWordLength(4)->withPadding(true);

        // Input has incomplete last word
        $bits = $converter->toBitString(['1011', '01']);
        $result = $converter->fromBitString($bits);

        // Should pad the last word
        $this->assertEquals(['1011', '0100'], $result);
    }

    public function testDefaultWordLength(): void
    {
        $converter = new CodewordConverter();
        $bits = BitString::fromString('1011010110110101');

        // Default word length is 8
        $codewords = $converter->fromBitString($bits);
        $this->assertEquals(['10110101', '10110101'], $codewords);
    }

    public function testChangeWordLengthMultipleTimes(): void
    {
        $converter = new CodewordConverter();
        $bits = BitString::fromString('10110101');

        $converter->withWordLength(2);
        $this->assertEquals(['10', '11', '01', '01'], $converter->fromBitString($bits));

        $converter->withWordLength(4);
        $this->assertEquals(['1011', '0101'], $converter->fromBitString($bits));

        $converter->withWordLength(8);
        $this->assertEquals(['10110101'], $converter->fromBitString($bits));
    }

    public function testPaddingToggle(): void
    {
        $converter = new CodewordConverter();
        $converter->withWordLength(4);
        $bits = BitString::fromString('101101');

        $converter->withPadding(true);
        $this->assertEquals(['1011', '0100'], $converter->fromBitString($bits));

        $converter->withPadding(false);
        $this->assertEquals(['1011', '01'], $converter->fromBitString($bits));
    }

    public function testLargeBitString(): void
    {
        $converter = new CodewordConverter();
        $converter->withWordLength(8);

        $large = str_repeat('10110101', 100); // 800 bits
        $bits = BitString::fromString($large);

        $codewords = $converter->fromBitString($bits);
        $this->assertCount(100, $codewords);
        foreach ($codewords as $codeword) {
            $this->assertEquals('10110101', $codeword);
        }
    }
}
