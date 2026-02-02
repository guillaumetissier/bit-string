<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString\Converter;

use Guillaumetissier\BitString\BitString;
use Guillaumetissier\BitString\BitStringImmutable;
use Guillaumetissier\BitString\BitStringInterface;

/**
 * Converter for codeword array format.
 */
final class CodewordConverter implements ConverterInterface
{
    public function __construct(private int $wordLength = 8, private bool $pad = true)
    {
    }

    /**
     * Convert an array of codewords to a BitString (mutable).
     *
     * @param mixed $value Array of binary strings
     *
     * @throws \InvalidArgumentException If value is not a valid array of codewords
     */
    public function toBitString(mixed $value): BitString
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Value must be an array');
        }

        $binary = implode('', $value);

        return BitString::fromString($binary);
    }

    /**
     * Convert an array of codewords to a BitStringImmutable.
     *
     * @param mixed $value Array of binary strings
     *
     * @throws \InvalidArgumentException If value is not a valid array of codewords
     */
    public function toBitStringImmutable(mixed $value): BitStringImmutable
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Value must be an array');
        }

        $binary = implode('', $value);

        return BitStringImmutable::fromString($binary);
    }

    /**
     * Convert a BitString to an array of codewords.
     *
     * @return array<string>
     */
    public function fromBitString(BitStringInterface $bits): array
    {
        if ($this->wordLength < 1) {
            throw new \InvalidArgumentException('Word length must be at least 1');
        }

        $binary = $bits->toString();
        $chunks = str_split($binary, $this->wordLength);

        if ($this->pad && !empty($chunks)) {
            $lastKey = array_key_last($chunks);
            if (strlen($chunks[$lastKey]) < $this->wordLength) {
                $chunks[$lastKey] = str_pad($chunks[$lastKey], $this->wordLength, '0');
            }
        }

        return $chunks;
    }

    /**
     * Set the length of each codeword.
     *
     * @param int $wordLength Length in bits
     */
    public function withWordLength(int $wordLength): self
    {
        $this->wordLength = $wordLength;

        return $this;
    }

    /**
     * Set whether to pad the last codeword with zeros.
     */
    public function withPadding(bool $pad): self
    {
        $this->pad = $pad;

        return $this;
    }
}
