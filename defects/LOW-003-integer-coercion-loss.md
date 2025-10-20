# LOW-003: Silent Data Loss in IntegerField Coercion

## Priority
**LOW** - Edge case behavior

## Category
Bug - Data Validation

## Affected File
`src/fields/core/IntegerField.php` - Lines 39-49

## Description
`IntegerField` uses `is_numeric()` to validate input before converting to integer with `intval()`. However, `is_numeric()` accepts floats and scientific notation, which are silently truncated.

## Problematic Code
```php
public function processFormData(array $valuelist)
{
    if ($valuelist) {
        if (is_numeric($valuelist[0])) {
            $this->data = intval($valuelist[0]);
        } else {
            $this->data = null;
            throw new ValueError("Not a valid integer value");
        }
    }
}
```

## Problem Cases

### Case 1: Float Input Truncated
```php
$field = new IntegerField(['name' => 'age']);
$field->process(['age' => '25.7']);

// is_numeric('25.7') = true
// intval('25.7') = 25
// User entered 25.7, but field silently stores 25
echo $field->data;  // Output: 25 (data loss!)
```

### Case 2: Scientific Notation
```php
$field = new IntegerField(['name' => 'count']);
$field->process(['count' => '1e10']);

// is_numeric('1e10') = true
// intval('1e10') = 1 (!)
echo $field->data;  // Output: 1 (incorrect!)
```

### Case 3: Large Numbers
```php
$field = new IntegerField(['name' => 'amount']);
$field->process(['amount' => '9999999999999999999']);

// is_numeric() = true
// intval() overflows to PHP_INT_MAX on 32-bit or weird values
echo $field->data;  // Platform-dependent overflow
```

## Impact
- Silent data loss (25.7 becomes 25)
- Incorrect values (1e10 becomes 1)
- Platform-dependent behavior
- Users confused why their input was changed

## Suggested Fix

### Option 1: Strict Integer Validation (Recommended)
```php
public function processFormData(array $valuelist)
{
    if ($valuelist) {
        $value = $valuelist[0];

        // Strict check: must be digits only (with optional sign)
        if (is_string($value) && preg_match('/^-?\d+$/', $value)) {
            $this->data = intval($value);
        } else {
            $this->data = null;
            throw new ValueError("Not a valid integer value");
        }
    }
}
```

### Option 2: Reject Floats Explicitly
```php
public function processFormData(array $valuelist)
{
    if ($valuelist) {
        $value = $valuelist[0];

        if (is_numeric($value)) {
            // Check if it's actually a float
            if (strpos($value, '.') !== false || strpos($value, 'e') !== false) {
                $this->data = null;
                throw new ValueError("Integer field does not accept decimal or scientific notation");
            }
            $this->data = intval($value);
        } else {
            $this->data = null;
            throw new ValueError("Not a valid integer value");
        }
    }
}
```

### Option 3: Use filter_var (Most Robust)
```php
public function processFormData(array $valuelist)
{
    if ($valuelist) {
        $value = $valuelist[0];

        // filter_var is strict about integers
        $filtered = filter_var($value, FILTER_VALIDATE_INT);

        if ($filtered !== false) {
            $this->data = $filtered;
        } else {
            $this->data = null;
            throw new ValueError("Not a valid integer value");
        }
    }
}
```

## Comparison of Approaches

| Input | is_numeric() | Regex | filter_var |
|-------|--------------|-------|------------|
| "123" | ✓ (123) | ✓ (123) | ✓ (123) |
| "-456" | ✓ (-456) | ✓ (-456) | ✓ (-456) |
| "25.7" | ✓ (25) ❌ | ✗ | ✗ |
| "1e10" | ✓ (1) ❌ | ✗ | ✗ |
| "0xFF" | ✓ (0) ❌ | ✗ | ✗ |
| " 123 " | ✓ (123) | ✗ | ✓ (123) |
| "123abc" | ✗ | ✗ | ✗ |

**Recommendation**: Use `filter_var(FILTER_VALIDATE_INT)` for most robust validation.

## FloatField Has Same Issue
`src/fields/core/FloatField.php` has similar problem:
```php
if (is_numeric($valuelist[0])) {
    $this->data = floatval($valuelist[0]);
}
```

Should also validate format more strictly (though floats naturally accept scientific notation).

## Testing Required
Add tests in `tests/fields/IntegerFieldTest.php`:

```php
/**
 * @expectedException \WTForms\Exceptions\ValueError
 * @expectedExceptionMessage Not a valid integer value
 */
public function testRejectsFloat()
{
    $field = new IntegerField(['name' => 'count']);
    $field->process(['count' => '25.7']);
    // Should throw, not silently truncate to 25
}

/**
 * @expectedException \WTForms\Exceptions\ValueError
 */
public function testRejectsScientificNotation()
{
    $field = new IntegerField(['name' => 'count']);
    $field->process(['count' => '1e10']);
    // Should throw, not convert to 1
}

public function testAcceptsNegativeInteger()
{
    $field = new IntegerField(['name' => 'temp']);
    $field->process(['temp' => '-42']);
    $this->assertEquals(-42, $field->data);
}

public function testAcceptsZero()
{
    $field = new IntegerField(['name' => 'count']);
    $field->process(['count' => '0']);
    $this->assertEquals(0, $field->data);
}
```

## Documentation
Update field docblock:

```php
/**
 * Integer field.
 *
 * Accepts string representations of integers only.
 * Does NOT accept:
 * - Decimal numbers (25.7)
 * - Scientific notation (1e10)
 * - Hexadecimal (0xFF)
 *
 * @package WTForms\Fields\Core
 */
class IntegerField extends Field
{
    // ...
}
```

## Migration Considerations
This is a **breaking change** if users rely on truncation behavior. Consider:
1. Add deprecation warning for float inputs in next minor version
2. Make strict in next major version
3. Document in CHANGELOG
