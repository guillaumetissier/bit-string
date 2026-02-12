<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString\Converter;

use Guillaumetissier\BitString\BitString;
use Guillaumetissier\BitString\BitStringImmutable;
use Guillaumetissier\BitString\BitStringInterface;

/**
 * Converter for binary string format.
 */
final class BitsConverter implements ConverterInterface
{
    /**
     * Convert an array of bits to a BitString (mutable).
     *
     * @param mixed $value array of bits (e.g., [1, 0, 0, 1, 1])
     *
     * @throws \InvalidArgumentException If value is not a valid binary string
     */
    public function toBitString(mixed $value): BitString
    {
        return BitString::fromString($this->toBinaryString($value));
    }

    /**
     * Convert an array of bits to a BitStringImmutable.
     *
     * @param mixed $value array of bits (e.g., [1, 0, 0, 1, 1])
     *
     * @throws \InvalidArgumentException If value is not a valid binary string
     */
    public function toBitStringImmutable(mixed $value): BitStringImmutable
    {
        return BitStringImmutable::fromString($this->toBinaryString($value));
    }

    /**
     * Convert a BitString to an array of bits.
     *
     * @return int[]
     */
    public function fromBitString(BitStringInterface $bitString): array
    {
        if (($string = $bitString->toString()) === '') {
            return [];
        }

        return array_map(static fn (string $b): int => intval($b), str_split($string));
    }

    private function toBinaryString(mixed $value): string
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Value must be an array');
        }

        if (!array_all($value, static fn ($v): bool => is_scalar($v))) {
            throw new \InvalidArgumentException('Value must be an array of scalar values');
        }

        return implode('', array_map(static fn (mixed $b): string => is_scalar($b) ? (string) intval($b) : '', $value));
    }
}
