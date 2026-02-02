<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString\Converter;

use Guillaumetissier\BitString\BitString;
use Guillaumetissier\BitString\BitStringImmutable;
use Guillaumetissier\BitString\BitStringInterface;

/**
 * Converter for hexadecimal string format.
 */
final class HexConverter implements ConverterInterface
{
    public function __construct(private bool $addPrefix = false)
    {
    }

    /**
     * Convert a hexadecimal string to a BitString (mutable).
     *
     * @param mixed $value Hexadecimal string (e.g., "B5" or "0xB5")
     *
     * @throws \InvalidArgumentException If value is not a valid hexadecimal string
     */
    public function toBitString(mixed $value): BitString
    {
        return BitString::fromString($this->hex2bin($value));
    }

    /**
     * Convert a hexadecimal string to a BitStringImmutable.
     *
     * @param mixed $value Hexadecimal string (e.g., "B5" or "0xB5")
     *
     * @throws \InvalidArgumentException If value is not a valid hexadecimal string
     */
    public function toBitStringImmutable(mixed $value): BitStringImmutable
    {
        return BitStringImmutable::fromString($this->hex2bin($value));
    }

    /**
     * Convert a BitString to a hexadecimal string.
     */
    public function fromBitString(BitStringInterface $bits): string
    {
        $binary = $bits->toString();

        // Pad to multiple of 4
        $padding = (4 - (strlen($binary) % 4)) % 4;
        $binary = str_repeat('0', $padding).$binary;

        $hex = '';
        foreach (str_split($binary, 4) as $chunk) {
            $hex .= dechex((int) bindec($chunk));
        }

        return ($this->addPrefix ? '0x' : '').strtoupper($hex);
    }

    /**
     * Set whether to add "0x" prefix to output.
     */
    public function withPrefix(bool $addPrefix = true): self
    {
        $this->addPrefix = $addPrefix;

        return $this;
    }

    private function hex2bin(mixed $value): string
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Value must be a string');
        }

        $hex = str_starts_with($value, '0x') ? ltrim($value, '0x') : $value;

        if (!ctype_xdigit($hex)) {
            throw new \InvalidArgumentException('Invalid hexadecimal string');
        }

        $binary = '';
        foreach (str_split($hex) as $char) {
            $binary .= str_pad(decbin((int) hexdec($char)), 4, '0', STR_PAD_LEFT);
        }

        return $binary;
    }
}
