# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-02-02

### Added

#### Core Classes
- `BitStringInterface` - Common interface for all BitString implementations
- `BitString` - Mutable implementation
- `BitStringImmutable` - Immutable implementation

#### Converter System
- `ConverterInterface` - Common interface for all converters
- `BinaryConverter` - Convert to/from binary strings
- `HexConverter` - Convert to/from hexadecimal strings with optional `0x` prefix
- `DecimalConverter` - Convert to/from decimal integers with optional fixed width
- `CodewordConverter` - Convert to/from arrays of codewords with configurable word length and padding
- All converters support both `toBitString()` (mutable) and `toBitStringImmutable()` methods

#### BitString Operations
- **Factory methods**: `fromString()`, `zeros()`, `ones()`
- **Bit access**: `get()`, `set()`, `flip()`
- **Bitwise operations**: `and()`, `or()`, `xor()`, `not()`
- **Shifts**: `shiftLeft()`, `shiftRight()` (linear)
- **Rotations**: `rotateLeft()`, `rotateRight()` (circular)
- **Concatenation**: `prepend()`, `append()`
- **Information**: `length()`, `bitCount()`, `popcount()`, `equals()`
- **Conversion**: `toString()`, `__toString()`

### Technical Details
- PHP 8.1+ requirement
- Strict typing throughout
- PSR-4 autoloading
- PSR-12 coding standards
- MIT License

[1.0.0]: https://github.com/your-vendor/bit-string/releases/tag/v1.0.0