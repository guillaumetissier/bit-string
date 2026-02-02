<?php

declare(strict_types=1);

namespace Guillaumetissier\BitString\Converter;

use Guillaumetissier\BitString\BitString;
use Guillaumetissier\BitString\BitStringImmutable;
use Guillaumetissier\BitString\BitStringInterface;

/**
 * Interface for bit string converters.
 */
interface ConverterInterface
{
    /**
     * Convert a value to a BitString (mutable).
     *
     * @param mixed $value Value to convert
     */
    public function toBitString(mixed $value): BitString;

    /**
     * Convert a value to a BitStringImmutable.
     *
     * @param mixed $value Value to convert
     */
    public function toBitStringImmutable(mixed $value): BitStringImmutable;

    /**
     * Convert a BitString to a value.
     *
     * @param BitStringInterface $bits BitString to convert
     */
    public function fromBitString(BitStringInterface $bits): mixed;
}
