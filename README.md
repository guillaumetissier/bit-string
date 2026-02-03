# BitString

A PHP library for manipulating bit strings with both mutable and immutable implementations.

## Features

- **Two implementations**: Mutable (`BitString`) and Immutable (`BitStringImmutable`)
- **Flexible conversion system**: Pluggable converters for different formats
- **Bitwise operations**: AND, OR, XOR, NOT, shifts, rotations
- **Type-safe**: Full type hints and strict typing
- **Well-tested**: Comprehensive test suite


## Installation

```bash
composer require guillaumetissier/bit-string
```

## Usage

### Creating BitStrings

```php
use BitString\BitString;
use BitString\BitStringImmutable;

// From binary string
$mutable = BitString::fromString('10110101');
$immutable = BitStringImmutable::fromString('10110101');

// Factory methods
$empty = BitSt:empty();                 // ''
$zeros = BitString::zeros(8);           // '00000000'
$ones = BitStringImmutable::ones(8);    // '11111111'
```

### Using Converters

```php
use BitString\Converter\BinaryConverter;
use BitString\Converter\HexConverter;
use BitString\Converter\DecimalConverter;
use BitString\Converter\CodewordConverter;

// Binary - returns mutable by default
$binConverter = new BinaryConverter();
$bits = $binConverter->toBitString('10110101');              // BitString (mutable)
$immutable = $binConverter->toBitStringImmutable('10110101'); // BitStringImmutable
$binary = $binConverter->fromBitString($bits);                // '10110101'

// Hexadecimal
$hexConverter = new HexConverter();
$bits = $hexConverter->toBitString('B5');                     // BitString (mutable)
$immutable = $hexConverter->toBitStringImmutable('B5');       // BitStringImmutable
$hex = $hexConverter->fromBitString($bits);                   // 'B5'

// With prefix
$hexConverter->withPrefix(true);
$hex = $hexConverter->fromBitString($bits);                   // '0xB5'

// Decimal
$decConverter = new DecimalConverter();
$bits = $decConverter->toBitString(181);                      // BitString (mutable)
$immutable = $decConverter->toBitStringImmutable(181);        // BitStringImmutable
$decimal = $decConverter->fromBitString($bits);               // 181

// With fixed width
$decConverter->withWidth(8);
$bits = $decConverter->toBitString(5);                        // '00000101'

// Codewords
$cwConverter = new CodewordConverter();
$cwConverter->withWordLength(4);
$bits = $cwConverter->toBitString(['1011', '0101']);          // BitString (mutable)
$immutable = $cwConverter->toBitStringImmutable(['1011', '0101']); // BitStringImmutable
$codewords = $cwConverter->fromBitString($bits);              // ['1011', '0101']
```

### Mutable vs Immutable

```php
// Mutable - modifies in place
$mutable = BitString::fromString('1100');
$mutable->not();                        // Mutates instance
echo $mutable->toString();              // '0011'

// Immutable - returns new instance
$immutable = BitStringImmutable::fromString('1100');
$result = $immutable->not();            // Returns new instance
echo $immutable->toString();            // '1100' (unchanged)
echo $result->toString();               // '0011' (new instance)

// Chaining with mutable
$bits = BitString::fromString('11')
    ->prepend(BitString::fromString('00'))
    ->append(BitString::fromString('11'))
    ->not();                            // All operations mutate
echo $bits->toString();                 // '110000'

// Chaining with immutable
$result = BitStringImmutable::fromString('11')
    ->prepend(BitStringImmutable::fromString('00'))
    ->append(BitStringImmutable::fromString('11'))
    ->not();                            // All operations return new instances
```

### Bitwise Operations

```php
$a = BitString::fromString('1100');
$b = BitString::fromString('1010');

$a->and($b);        // '1000' (mutates $a)
$a->or($b);         // '1110' (mutates $a)
$a->xor($b);        // '0110' (mutates $a)
$a->not();          // '0011' (mutates $a)
```

### Bit Manipulation

```php
$bits = BitString::fromString('1011');

// Access
$bit = $bits->get(0);                   // 1
$length = $bits->length();              // 4
$count = $bits->bitCount();             // 4 (alias)
$ones = $bits->popCount();              // 3

// Modify (mutable)
$bits->set(1, 1);                       // Mutates: '1111'
$bits->flip(0);                         // Mutates: '0111'

// Concatenate
$bits->prepend(BitString::fromString('00'));  // Mutates: '000111'
$bits->append(BitString::fromString('11'));   // Mutates: '00011111'
```

### Shifts and Rotations

```php
$bits = BitString::fromString('10110101');

$bits->shiftLeft(2);        // '11010100'
$bits->shiftRight(2);       // '00101101'
$bits->rotateLeft(2);       // '11010110'
$bits->rotateRight(2);      // '01101101'
```

### Extraction
```php
$bits = BitString::fromString('10110101');

// Extract N bits from a position
$bits->extract(2, 4);                   // '1101'

// Slice an interval [start, end)
$bits->slice(2, 6);                     // '1101'

// First / last N bits
$bits->first(3);                        // '101'
$bits->last(3);                         // '101'

// Single codeword at index
$bits->codeword(0, 4);                  // '1011'
$bits->codeword(1, 4);                  // '0101'

// Extraction is composable — extract then split
$bits->slice(2, 6)->first(2);           // '11'
```

All extraction methods return the same type as the source (`BitString` or `BitStringImmutable`), and work identically on both implementations.

## When to Use Mutable vs Immutable

**Use `BitString` (mutable) when:**
- You need to modify bits in loops (better performance)
- Working with large bit strings that change frequently
- Building bit strings incrementally
- Performance is critical

**Use `BitStringImmutable` when:**
- Thread safety is important
- You want to avoid side effects
- Working with functional programming patterns
- The bit string represents a value that shouldn't change

## Requirements

- PHP 8.1 or higher

## Testing

```bash
composer test
```

## License

MIT License