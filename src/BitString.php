<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString;

/**
 * Mutable representation of a string of bits (0s and 1s).
 */
final class BitString extends AbstractBitString
{
    /**
     * Create a BitString from a binary string.
     *
     * @param string $binary Binary string (e.g., "10110101")
     *
     * @throws \InvalidArgumentException If the string contains non-binary characters
     */
    public static function fromString(string $binary): static
    {
        return new BitString($binary);
    }

    /**
     * Create an empty BitString.
     */
    public static function empty(): self
    {
        return new self('');
    }

    /**
     * Create a BitString with all bits set to 0.
     *
     * @param int $length Number of bits
     *
     * @throws \InvalidArgumentException If length is negative
     */
    public static function zeros(int $length): self
    {
        self::assertValidLength($length);

        return new self(str_repeat('0', $length));
    }

    /**
     * Create a BitString with all bits set to 1.
     *
     * @param int $length Number of bits
     *
     * @throws \InvalidArgumentException If length is negative
     */
    public static function ones(int $length): self
    {
        self::assertValidLength($length);

        return new self(str_repeat('1', $length));
    }

    /**
     * Set the bit at the specified index (mutates the instance).
     *
     * @param int $index Zero-based index
     * @param int $value 0 or 1
     *
     * @return self Returns $this for chaining
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

        $this->bits[$index] = (string) $value;

        return $this;
    }

    /**
     * Flip the bit at the specified index (mutates the instance).
     *
     * @param int $index Zero-based index
     *
     * @return self Returns $this for chaining
     */
    public function flip(int $index): self
    {
        return $this->set($index, 0 === $this->get($index) ? 1 : 0);
    }

    /**
     * Perform bitwise AND operation (mutates the instance).
     *
     * @return self Returns $this for chaining
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

        $this->bits = $result;

        return $this;
    }

    /**
     * Perform bitwise OR operation (mutates the instance).
     *
     * @return self Returns $this for chaining
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

        $this->bits = $result;

        return $this;
    }

    /**
     * Perform bitwise XOR operation (mutates the instance).
     *
     * @return self Returns $this for chaining
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

        $this->bits = $result;

        return $this;
    }

    /**
     * Perform bitwise NOT operation (mutates the instance).
     *
     * @return self Returns $this for chaining
     */
    public function not(): self
    {
        $result = '';
        $length = strlen($this->bits);
        for ($i = 0; $i < $length; ++$i) {
            $result .= '0' === $this->bits[$i] ? '1' : '0';
        }

        $this->bits = $result;

        return $this;
    }

    /**
     * Shift bits to the left (mutates the instance).
     *
     * @param int  $positions Number of positions to shift
     * @param bool $circular  Whether to perform circular shift (rotate)
     *
     * @return self Returns $this for chaining
     */
    public function shiftLeft(int $positions, bool $circular = false): self
    {
        $length = strlen($this->bits);
        $positions = $positions % $length;

        if ($circular) {
            return $this->rotateLeft($positions);
        }

        $this->bits = substr($this->bits, $positions).str_repeat('0', $positions);

        return $this;
    }

    /**
     * Shift bits to the right (mutates the instance).
     *
     * @param int  $positions Number of positions to shift
     * @param bool $circular  Whether to perform circular shift (rotate)
     *
     * @return self Returns $this for chaining
     */
    public function shiftRight(int $positions, bool $circular = false): self
    {
        $length = strlen($this->bits);
        $positions = $positions % $length;

        if ($circular) {
            return $this->rotateRight($positions);
        }

        $this->bits = str_repeat('0', $positions).substr($this->bits, 0, -$positions);

        return $this;
    }

    /**
     * Rotate bits to the left (mutates the instance).
     *
     * @param int $positions Number of positions to rotate
     *
     * @return self Returns $this for chaining
     */
    public function rotateLeft(int $positions): self
    {
        $length = strlen($this->bits);
        $positions = $positions % $length;

        $this->bits = substr($this->bits, $positions).substr($this->bits, 0, $positions);

        return $this;
    }

    /**
     * Rotate bits to the right (mutates the instance).
     *
     * @param int $positions Number of positions to rotate
     *
     * @return self Returns $this for chaining
     */
    public function rotateRight(int $positions): self
    {
        $length = strlen($this->bits);
        $positions = $positions % $length;

        $this->bits = substr($this->bits, -$positions).substr($this->bits, 0, -$positions);

        return $this;
    }

    /**
     * Prepend another BitString or string to the beginning (mutates the instance).
     *
     * @param BitStringInterface|string $other BitString or string to prepend
     *
     * @return self Returns $this for chaining
     */
    public function prepend(BitStringInterface|string $other): self
    {
        if (is_string($other)) {
            $this->validate($other);
            $this->bits = $other.$this->bits;
        } else {
            $this->bits = $other->toString().$this->bits;
        }

        return $this;
    }

    /**
     * Append another BitString or string to the end (mutates the instance).
     *
     * @param BitStringInterface|string $other BitString or string to append
     *
     * @return self Returns $this for chaining
     */
    public function append(BitStringInterface|string $other): self
    {
        if (is_string($other)) {
            $this->validate($other);
            $this->bits .= $other;
        } else {
            $this->bits .= $other->toString();
        }

        return $this;
    }
}
