<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString;

/**
 * Immutable representation of a string of bits (0s and 1s).
 */
final class BitStringImmutable implements BitStringInterface
{
    /**
     * @param string $bits Binary string (e.g., "10110101")
     */
    private function __construct(private string $bits)
    {
        $this->validate();
    }

    /**
     * Create a BitString from a binary string.
     *
     * @param string $binary Binary string (e.g., "10110101")
     *
     * @throws \InvalidArgumentException If the string contains non-binary characters
     */
    public static function fromString(string $binary): self
    {
        if ('' === $binary) {
            throw new \InvalidArgumentException('Binary string cannot be empty');
        }

        if (!preg_match('/^[01]+$/', $binary)) {
            throw new \InvalidArgumentException('Binary string must contain only 0 and 1');
        }

        return new self($binary);
    }

    /**
     * Create a BitString with all bits set to 0.
     *
     * @param int $length Number of bits
     */
    public static function zeros(int $length): self
    {
        if ($length < 1) {
            throw new \InvalidArgumentException('Length must be at least 1');
        }

        return new self(str_repeat('0', $length));
    }

    /**
     * Create a BitString with all bits set to 1.
     *
     * @param int $length Number of bits
     */
    public static function ones(int $length): self
    {
        if ($length < 1) {
            throw new \InvalidArgumentException('Length must be at least 1');
        }

        return new self(str_repeat('1', $length));
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
     * Set the bit at the specified index.
     *
     * @param int $index Zero-based index
     * @param int $value 0 or 1
     *
     * @return self New BitString instance
     *
     * @throws \OutOfBoundsException     If index is out of bounds
     * @throws \InvalidArgumentException If value is not 0 or 1
     */
    public function set(int $index, int $value): self
    {
        if ($index < 0 || $index >= strlen($this->bits)) {
            throw new \OutOfBoundsException('Index out of bounds');
        }

        if (0 !== $value && 1 !== $value) {
            throw new \InvalidArgumentException('Value must be 0 or 1');
        }

        $newBits = $this->bits;
        $newBits[$index] = (string) $value;

        return new self($newBits);
    }

    /**
     * Flip the bit at the specified index.
     *
     * @param int $index Zero-based index
     *
     * @return self New BitString instance
     */
    public function flip(int $index): self
    {
        return $this->set($index, 0 === $this->get($index) ? 1 : 0);
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
     * Perform bitwise AND operation.
     *
     * @throws \InvalidArgumentException If bit strings have different lengths
     */
    public function and(BitStringInterface $other): self
    {
        $this->assertSameLength($other);

        $result = '';
        $length = strlen($this->bits);
        $otherBits = $other->toString();
        for ($i = 0; $i < $length; ++$i) {
            $result .= ('1' === $this->bits[$i] && '1' === $otherBits[$i]) ? '1' : '0';
        }

        return new self($result);
    }

    /**
     * Perform bitwise OR operation.
     *
     * @throws \InvalidArgumentException If bit strings have different lengths
     */
    public function or(BitStringInterface $other): self
    {
        $this->assertSameLength($other);

        $result = '';
        $length = strlen($this->bits);
        $otherBits = $other->toString();
        for ($i = 0; $i < $length; ++$i) {
            $result .= ('1' === $this->bits[$i] || '1' === $otherBits[$i]) ? '1' : '0';
        }

        return new self($result);
    }

    /**
     * Perform bitwise XOR operation.
     *
     * @throws \InvalidArgumentException If bit strings have different lengths
     */
    public function xor(BitStringInterface $other): self
    {
        $this->assertSameLength($other);

        $result = '';
        $length = strlen($this->bits);
        $otherBits = $other->toString();
        for ($i = 0; $i < $length; ++$i) {
            $result .= ($this->bits[$i] !== $otherBits[$i]) ? '1' : '0';
        }

        return new self($result);
    }

    /**
     * Perform bitwise NOT operation.
     */
    public function not(): self
    {
        $result = '';
        $length = strlen($this->bits);
        for ($i = 0; $i < $length; ++$i) {
            $result .= '0' === $this->bits[$i] ? '1' : '0';
        }

        return new self($result);
    }

    /**
     * Shift bits to the left.
     *
     * @param int  $positions Number of positions to shift
     * @param bool $circular  Whether to perform circular shift (rotate)
     */
    public function shiftLeft(int $positions, bool $circular = false): self
    {
        $length = strlen($this->bits);
        $positions = $positions % $length;

        if ($circular) {
            return $this->rotateLeft($positions);
        }

        $newBits = substr($this->bits, $positions).str_repeat('0', $positions);

        return new self($newBits);
    }

    /**
     * Shift bits to the right.
     *
     * @param int  $positions Number of positions to shift
     * @param bool $circular  Whether to perform circular shift (rotate)
     */
    public function shiftRight(int $positions, bool $circular = false): self
    {
        $length = strlen($this->bits);
        $positions = $positions % $length;

        if ($circular) {
            return $this->rotateRight($positions);
        }

        $newBits = str_repeat('0', $positions).substr($this->bits, 0, -$positions);

        return new self($newBits);
    }

    /**
     * Rotate bits to the left (circular shift).
     *
     * @param int $positions Number of positions to rotate
     */
    public function rotateLeft(int $positions): self
    {
        $length = strlen($this->bits);
        $positions = $positions % $length;

        $newBits = substr($this->bits, $positions).substr($this->bits, 0, $positions);

        return new self($newBits);
    }

    /**
     * Rotate bits to the right (circular shift).
     *
     * @param int $positions Number of positions to rotate
     */
    public function rotateRight(int $positions): self
    {
        $length = strlen($this->bits);
        $positions = $positions % $length;

        $newBits = substr($this->bits, -$positions).substr($this->bits, 0, -$positions);

        return new self($newBits);
    }

    /**
     * Count the number of 1 bits (population count).
     */
    public function popCount(): int
    {
        return substr_count($this->bits, '1');
    }

    /**
     * Prepend another BitString to the beginning.
     *
     * @param BitStringInterface $other BitString to prepend
     *
     * @return self New BitString instance
     */
    public function prepend(BitStringInterface $other): self
    {
        return new self($other->toString().$this->bits);
    }

    /**
     * Append another BitString to the end.
     *
     * @param BitStringInterface $other BitString to append
     *
     * @return self New BitString instance
     */
    public function append(BitStringInterface $other): self
    {
        return new self($this->bits.$other->toString());
    }

    /**
     * Check if this BitString equals another.
     */
    public function equals(BitStringInterface $other): bool
    {
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
     * Validate that all characters are 0 or 1.
     *
     * @throws \InvalidArgumentException
     */
    private function validate(): void
    {
        if (!preg_match('/^[01]+$/', $this->bits)) {
            throw new \InvalidArgumentException('All bits must be 0 or 1');
        }
    }

    /**
     * Assert that another BitString has the same length.
     *
     * @throws \InvalidArgumentException
     */
    private function assertSameLength(BitStringInterface $other): void
    {
        if (strlen($this->bits) !== $other->length()) {
            throw new \InvalidArgumentException('Bit strings must have the same length');
        }
    }
}
