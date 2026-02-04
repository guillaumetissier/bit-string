# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.3.1] - 2026-02-04

### Fixed
- Validate string before appending/prepending to `BitString` or `BitStringImmutable`

## [1.3.0] - 2026-02-04

### Added
- `BitStringInterface` now extends `Stringable` for native PHP type compatibility
- `append()`, `prepend()`, and `equals()` now accept `string` parameters in addition to `BitStringInterface`

### Changed
- Enhanced method signatures for better ergonomics:
    - `append(BitStringInterface|string $other): self`
    - `prepend(BitStringInterface|string $other): self`
    - `equals(BitStringInterface|string $other): bool`

## [1.2.0] - 2026-02-03

### Added
- Extraction methods in `AbstractBitString`: `extract()`, `slice()`, `first()`, `last()`, `codeword()`
- `empty()` factory method to create an empty BitString
- `AbstractBitString` as shared base class for `BitString` and `BitStringImmutable`

### Changed
- `BitString` and `BitStringImmutable` now extend `AbstractBitString`
- `zeros()` and `ones()` now accept `0` as length parameter
- `validate()` no longer rejects empty bit strings

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