<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString;

/**
 * Abstract base class for BitString implementations.
 * Contains shared logic and extraction methods.
 */
abstract class AbstractBitString implements BitStringInterface
{
    public function __construct(protected string $bits)
    {
        $this->validate();
    }

    /**
     * Get the internal binary string representation.
     *
     * @internal Used by converters
     */
    public function toString(): string
    {
        return $this->bits;
    }

    /**
     * Get the bit at the specified index.
     *
     * @param int $index Zero-based index
     *
     * @return int 0 or 1
     *
     * @throws \OutOfBoundsException If index is out of bounds
     */
    public function get(int $index): int
    {
        if ($index < 0 || $index >= strlen($this->bits)) {
            throw new \OutOfBoundsException('Index out of bounds');
        }

        return (int) $this->bits[$index];
    }

    /**
     * Get the length (number of bits).
     */
    public function length(): int
    {
        return strlen($this->bits);
    }

    /**
     * Get the number of bits (alias for length()).
     */
    public function bitCount(): int
    {
        return strlen($this->bits);
    }

    /**
     * Count the number of 1 bit (population count).
     */
    public function popCount(): int
    {
        return substr_count($this->bits, '1');
    }

    /**
     * Check if this BitString equals another BitString or string.
     */
    public function equals(BitStringInterface|string $other): bool
    {
        if (is_string($other)) {
            return $this->bits === $other;
        }

        return $this->bits === $other->toString();
    }

    /**
     * Convert to string representation.
     */
    public function __toString(): string
    {
        return $this->bits;
    }

    /**
     * Extract a sub-sequence of bits starting at a given position.
     *
     * @param int $position Starting position (zero-based)
     * @param int $length   Number of bits to extract
     *
     * @throws \OutOfBoundsException     If position or length is out of bounds
     * @throws \InvalidArgumentException If length is less than 1
     */
    public function extract(int $position, int $length): BitStringInterface
    {
        if ($length < 1) {
            throw new \InvalidArgumentException('Length must be at least 1');
        }

        if ($position < 0 || $position >= strlen($this->bits)) {
            throw new \OutOfBoundsException('Position out of bounds');
        }

        if ($position + $length > strlen($this->bits)) {
            throw new \OutOfBoundsException('Extraction exceeds bit string length');
        }

        return static::fromString(substr($this->bits, $position, $length));
    }

    /**
     * Extract a sub-sequence of bits within an interval [start, end).
     *
     * @param int $start Start index (inclusive, zero-based)
     * @param int $end   End index (exclusive)
     *
     * @throws \OutOfBoundsException     If start or end is out of bounds
     * @throws \InvalidArgumentException If start >= end
     */
    public function slice(int $start, int $end): BitStringInterface
    {
        if ($start >= $end) {
            throw new \InvalidArgumentException('Start must be less than end');
        }

        if ($start < 0 || $start >= strlen($this->bits)) {
            throw new \OutOfBoundsException('Start position out of bounds');
        }

        if ($end > strlen($this->bits)) {
            throw new \OutOfBoundsException('End position out of bounds');
        }

        return static::fromString(substr($this->bits, $start, $end - $start));
    }

    /**
     * Extract the first N bits.
     *
     * @param int $length Number of bits to extract from the beginning
     *
     * @throws \OutOfBoundsException     If length exceeds bit string length
     * @throws \InvalidArgumentException If length is less than 1
     */
    public function first(int $length): BitStringInterface
    {
        if ($length < 1) {
            throw new \InvalidArgumentException('Length must be at least 1');
        }

        if ($length > strlen($this->bits)) {
            throw new \OutOfBoundsException('Length exceeds bit string length');
        }

        return static::fromString(substr($this->bits, 0, $length));
    }

    /**
     * Extract the last N bits.
     *
     * @param int $length Number of bits to extract from the end
     *
     * @throws \OutOfBoundsException     If length exceeds bit string length
     * @throws \InvalidArgumentException If length is less than 1
     */
    public function last(int $length): BitStringInterface
    {
        if ($length < 1) {
            throw new \InvalidArgumentException('Length must be at least 1');
        }

        if ($length > strlen($this->bits)) {
            throw new \OutOfBoundsException('Length exceeds bit string length');
        }

        return static::fromString(substr($this->bits, -$length));
    }

    /**
     * Extract a codeword at a given index.
     *
     * @param int $index      Codeword index (zero-based)
     * @param int $wordLength Length of each codeword in bits
     *
     * @throws \InvalidArgumentException If wordLength is less than 1
     * @throws \OutOfBoundsException     If the codeword index is out of bounds
     */
    public function codeword(int $index, int $wordLength): BitStringInterface
    {
        if ($wordLength < 1) {
            throw new \InvalidArgumentException('Word length must be at least 1');
        }

        $position = $index * $wordLength;

        if ($position < 0 || $position >= strlen($this->bits)) {
            throw new \OutOfBoundsException('Codeword index out of bounds');
        }

        $available = strlen($this->bits) - $position;
        $actualLength = min($wordLength, $available);

        return static::fromString(substr($this->bits, $position, $actualLength));
    }

    /**
     * Validate that all characters are 0 or 1.
     *
     * @throws \InvalidArgumentException
     */
    protected function validate(): void
    {
        if ('' !== $this->bits && !preg_match('/^[01]+$/', $this->bits)) {
            throw new \InvalidArgumentException('Binary string must contain only 0 and 1');
        }
    }

    /**
     * Assert that another BitString has the same length.
     *
     * @throws \InvalidArgumentException
     */
    protected function assertSameLength(BitStringInterface $other): void
    {
        if (strlen($this->bits) !== $other->length()) {
            throw new \InvalidArgumentException('Bit strings must have the same length');
        }
    }

    abstract public static function fromString(string $binary): static;

    /**
     * Validate the length parameter for factory methods.
     *
     * @throws \InvalidArgumentException If length is negative
     */
    protected static function assertValidLength(int $length): void
    {
        if ($length < 0) {
            throw new \InvalidArgumentException('Length must be positive');
        }
    }
}
