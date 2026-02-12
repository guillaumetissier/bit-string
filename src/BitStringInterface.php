<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString;

/**
 * Interface for bit string implementations.
 */
interface BitStringInterface extends \Stringable
{
    /**
     * Get the bit at the specified index.
     *
     * @param int $index Zero-based index
     *
     * @return int 0 or 1
     *
     * @throws \OutOfBoundsException If index is out of bounds
     */
    public function get(int $index): int;

    /**
     * Set the bit at the specified index.
     * Note: Returns new instance for immutable, same instance for mutable.
     *
     * @param int $index Zero-based index
     * @param int $value 0 or 1
     *
     * @throws \OutOfBoundsException     If index is out of bounds
     * @throws \InvalidArgumentException If value is not 0 or 1
     */
    public function set(int $index, int $value): self;

    /**
     * Flip the bit at the specified index.
     * Note: Returns new instance for immutable, same instance for mutable.
     *
     * @param int $index Zero-based index
     */
    public function flip(int $index): self;

    /**
     * Get the length (number of bits).
     */
    public function length(): int;

    /**
     * Get the number of bits (alias for length()).
     */
    public function bitCount(): int;

    /**
     * Perform bitwise AND operation.
     * Note: Returns new instance for immutable, same instance for mutable.
     *
     * @throws \InvalidArgumentException If bit strings have different lengths
     */
    public function and(BitStringInterface|string $other): self;

    /**
     * Perform bitwise OR operation.
     * Note: Returns new instance for immutable, same instance for mutable.
     *
     * @throws \InvalidArgumentException If bit strings have different lengths
     */
    public function or(BitStringInterface|string $other): self;

    /**
     * Perform bitwise XOR operation.
     * Note: Returns new instance for immutable, same instance for mutable.
     *
     * @throws \InvalidArgumentException If bit strings have different lengths
     */
    public function xor(BitStringInterface|string $other): self;

    /**
     * Perform bitwise NOT operation.
     * Note: Returns new instance for immutable, same instance for mutable.
     */
    public function not(): self;

    /**
     * Shift bits to the left.
     * Note: Returns new instance for immutable, same instance for mutable.
     *
     * @param int  $positions Number of positions to shift
     * @param bool $circular  Whether to perform circular shift (rotate)
     */
    public function shiftLeft(int $positions, bool $circular = false): self;

    /**
     * Shift bits to the right.
     * Note: Returns new instance for immutable, same instance for mutable.
     *
     * @param int  $positions Number of positions to shift
     * @param bool $circular  Whether to perform circular shift (rotate)
     */
    public function shiftRight(int $positions, bool $circular = false): self;

    /**
     * Rotate bits to the left (circular shift).
     * Note: Returns new instance for immutable, same instance for mutable.
     *
     * @param int $positions Number of positions to rotate
     */
    public function rotateLeft(int $positions): self;

    /**
     * Rotate bits to the right (circular shift).
     * Note: Returns new instance for immutable, same instance for mutable.
     *
     * @param int $positions Number of positions to rotate
     */
    public function rotateRight(int $positions): self;

    /**
     * Count the number of 1 bit (population count).
     */
    public function popCount(): int;

    /**
     * Prepend another BitString or string to the beginning.
     * Note: Returns new instance for immutable, same instance for mutable.
     *
     * @param self|string $other BitString to prepend
     */
    public function prepend(BitStringInterface|string $other): self;

    /**
     * Append another BitString or string to the end.
     * Note: Returns new instance for immutable, same instance for mutable.
     *
     * @param self|string $other BitString to append
     */
    public function append(BitStringInterface|string $other): self;

    /**
     * Check if this BitString equals another or string.
     *
     * @param self|string $other BitString or string to compare to
     */
    public function equals(BitStringInterface|string $other): bool;

    /**
     * pad the bit string with 0s.
     *
     * @param int  $length  if the value of pad_length is negative, less than,
     *                      or equal to the length of the input string, no padding takes place
     * @param bool $prepend if true, 0s are prepended to the bit string, else they are appended
     */
    public function pad(int $length, bool $prepend = true): self;

    /**
     * Get the internal binary string representation.
     */
    public function toString(): string;

    /**
     * Extract a sub-sequence of bits starting at a given position.
     *
     * @param int $position Starting position (zero-based)
     * @param int $length   Number of bits to extract
     *
     * @throws \OutOfBoundsException     If position or length is out of bounds
     * @throws \InvalidArgumentException If length is less than 1
     */
    public function extract(int $position, int $length): self;

    /**
     * Extract a sub-sequence of bits within an interval [start, end).
     *
     * @param int $start Start index (inclusive, zero-based)
     * @param int $end   End index (exclusive)
     *
     * @throws \OutOfBoundsException     If start or end is out of bounds
     * @throws \InvalidArgumentException If start >= end
     */
    public function slice(int $start, int $end): self;

    /**
     * Extract the first N bits.
     *
     * @param int $length Number of bits to extract from the beginning
     *
     * @throws \OutOfBoundsException     If length exceeds bit string length
     * @throws \InvalidArgumentException If length is less than 1
     */
    public function first(int $length): self;

    /**
     * Extract the last N bits.
     *
     * @param int $length Number of bits to extract from the end
     *
     * @throws \OutOfBoundsException     If length exceeds bit string length
     * @throws \InvalidArgumentException If length is less than 1
     */
    public function last(int $length): self;

    /**
     * Extract a codeword at a given index.
     *
     * @param int $index      Codeword index (zero-based)
     * @param int $wordLength Length of each codeword in bits
     *
     * @throws \InvalidArgumentException If wordLength is less than 1
     * @throws \OutOfBoundsException     If the codeword index is out of bounds
     */
    public function codeword(int $index, int $wordLength): self;
}
