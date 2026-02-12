<?php

namespace Guillaumetissier\BitString\Tests\Converter;

use Guillaumetissier\BitString\BitString;
use Guillaumetissier\BitString\BitStringImmutable;
use Guillaumetissier\BitString\BitStringInterface;
use Guillaumetissier\BitString\Converter\BitsConverter;
use PHPUnit\Framework\TestCase;

class BitsConverterTest extends TestCase
{
    private BitsConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new BitsConverter();
    }

    /**
     * @param array<int|string|float|bool> $bits
     *
     * @dataProvider dataToBitString
     */
    public function testToBitString(array $bits, string $expected): void
    {
        $this->assertInstanceOf(BitString::class, $actual = $this->converter->toBitString($bits));
        $this->assertEquals($expected, (string) $actual);
    }

    /**
     * @param array<int|string|float|bool> $bits
     *
     * @dataProvider dataToBitString
     */
    public function testToBitStringImmutable(array $bits, string $expected): void
    {
        $this->assertInstanceOf(BitStringImmutable::class, $actual = $this->converter->toBitStringImmutable($bits));
        $this->assertEquals($expected, (string) $actual);
    }

    public static function dataToBitString(): \Generator
    {
        yield [[], ''];
        yield [[1], '1'];
        yield [[1, 0, 0, 1, 0, 1], '100101'];
        yield [['1', '1', '0', '1', '0', '0', '1'], '1101001'];
        yield [[0.0, 1.0, 1.1, 1.999, 0.0, 1, 1.0], '0111011'];
        yield [[true, true, true, false, false, false, false, false], '11100000'];
    }

    /**
     * @param int[] $expected
     *
     * @dataProvider dataFromBitString
     */
    public function testFromBitString(BitStringInterface $bits, array $expected): void
    {
        $this->assertEquals($expected, $this->converter->fromBitString($bits));
    }

    public static function dataFromBitString(): \Generator
    {
        yield [BitString::empty(), []];
        yield [BitStringImmutable::fromString('1'), [1]];
        yield [BitString::fromString('100101'), [1, 0, 0, 1, 0, 1]];
        yield [BitStringImmutable::zeros(4), [0, 0, 0, 0]];
        yield [BitStringImmutable::ones(8), [1, 1, 1, 1, 1, 1, 1, 1]];
    }

    /**
     * @dataProvider dataFromBitStringImmutable
     */
    public function testToBitStringExceptionRaised(mixed $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->converter->toBitString($value);
    }

    public static function dataFromBitStringImmutable(): \Generator
    {
        yield [null];
        yield [true];
        yield [1];
        yield ['1010101010'];
        yield [1, 1, 0, [1]];
    }
}
