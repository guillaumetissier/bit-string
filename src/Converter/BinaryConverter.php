<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString\Converter;

use Guillaumetissier\BitString\BitString;
use Guillaumetissier\BitString\BitStringImmutable;
use Guillaumetissier\BitString\BitStringInterface;

/**
 * Converter for binary string format.
 */
final class BinaryConverter implements ConverterInterface
{
    /**
     * Convert a binary string to a BitString (mutable).
     *
     * @param mixed $value Binary string (e.g., "10110101")
     *
     * @throws \InvalidArgumentException If value is not a valid binary string
     */
    public function toBitString(mixed $value): BitString
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Value must be a string');
        }

        return BitString::fromString($value);
    }

    /**
     * Convert a binary string to a BitStringImmutable.
     *
     * @param mixed $value Binary string (e.g., "10110101")
     *
     * @throws \InvalidArgumentException If value is not a valid binary string
     */
    public function toBitStringImmutable(mixed $value): BitStringImmutable
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Value must be a string');
        }

        return BitStringImmutable::fromString($value);
    }

    /**
     * Convert a BitString to a binary string.
     */
    public function fromBitString(BitStringInterface $bits): string
    {
        return $bits->toString();
    }
}
