<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString\Converter;

use Guillaumetissier\BitString\BitString;
use Guillaumetissier\BitString\BitStringImmutable;
use Guillaumetissier\BitString\BitStringInterface;

/**
 * Converter for decimal integer format.
 */
final class DecimalConverter implements ConverterInterface
{
    public function __construct(private ?int $width = null)
    {
    }

    /**
     * Convert a decimal number to a BitString (mutable).
     *
     * @param mixed $value Decimal number (int)
     *
     * @throws \InvalidArgumentException If value is not a valid decimal number
     */
    public function toBitString(mixed $value): BitString
    {
        return BitString::fromString($this->dec2bin($value));
    }

    /**
     * Convert a decimal number to a BitStringImmutable.
     *
     * @param mixed $value Decimal number (int)
     *
     * @throws \InvalidArgumentException If value is not a valid decimal number
     */
    public function toBitStringImmutable(mixed $value): BitStringImmutable
    {
        return BitStringImmutable::fromString($this->dec2bin($value));
    }

    /**
     * Convert a BitString to a decimal number.
     */
    public function fromBitString(BitStringInterface $bitString): int
    {
        return (int) bindec($bitString->toString());
    }

    /**
     * Set the fixed width for output (padding with zeros).
     *
     * @param int|null $width Width in bits, null for minimum width
     */
    public function withWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    private function dec2bin(mixed $value): string
    {
        if (!is_int($value)) {
            throw new \InvalidArgumentException('Value must be an integer');
        }

        if ($value < 0) {
            throw new \InvalidArgumentException('Decimal number must be non-negative');
        }

        $binary = 0 === $value ? '0' : decbin($value);

        if (null !== $this->width) {
            if (strlen($binary) > $this->width) {
                throw new \InvalidArgumentException('Width is too small for the decimal value');
            }
            $binary = str_pad($binary, $this->width, '0', STR_PAD_LEFT);
        }

        return $binary;
    }
}
