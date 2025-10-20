# MEDIUM-003: Field.__get() Returns Null for Undefined Properties

## Priority
**MEDIUM** - Debugging difficulty

## Category
Bug - Error Handling

## Affected File
`src/fields/core/Field.php` - Lines 479-492

## Description
The `Field::__get()` magic method returns `null` for any undefined property access. This silently masks typos and makes debugging difficult.

## Problematic Code
```php
public function __get($name)
{
    if ($name == "value") {
        if ($this->raw_data) {
            return $this->raw_data[0];
        } elseif ($this->data !== null) {
            return strval($this->data);
        }
        return "";
    }

    return null;  // LINE 491 - Silent failure for typos!
}
```

## Problem Examples

### Example 1: Typo in Property Name
```php
$field = new StringField(['name' => 'username', 'data' => 'john']);

echo $field->dataa;  // Typo: "dataa" instead of "data"
// Output: null (should throw exception)

echo $field->valu;   // Typo: "valu" instead of "value"
// Output: null (should throw exception)
```

### Example 2: Silent Failures in Logic
```php
if ($field->requried) {  // Typo: "requried" instead of "required"
    // This branch never executes
    // Developer thinks field is not required, but really just typo
}
```

### Example 3: Debugging Nightmare
```php
$errors = $field->erors;  // Typo: "erors" instead of "errors"
if (count($errors)) {  // Warning: count() expects array, null given
    // ...
}
// Developer confused why count() fails
```

## Impact
- Typos go unnoticed until unexpected behavior occurs
- Debugging is difficult (null could mean many things)
- Code becomes fragile
- IDE warnings are ignored (property access on null)

## Suggested Fix

### Option 1: Throw Exception (Strict)
```php
public function __get($name)
{
    if ($name == "value") {
        if ($this->raw_data) {
            return $this->raw_data[0];
        } elseif ($this->data !== null) {
            return strval($this->data);
        }
        return "";
    }

    throw new \OutOfBoundsException(
        "Property '$name' does not exist on " . get_class($this)
    );
}
```

### Option 2: Trigger Warning (Less Strict)
```php
public function __get($name)
{
    if ($name == "value") {
        if ($this->raw_data) {
            return $this->raw_data[0];
        } elseif ($this->data !== null) {
            return strval($this->data);
        }
        return "";
    }

    trigger_error(
        "Undefined property: " . get_class($this) . "::$$name",
        E_USER_NOTICE
    );
    return null;
}
```

### Option 3: Whitelist Known Virtual Properties (Recommended)
```php
public function __get($name)
{
    switch ($name) {
        case "value":
            if ($this->raw_data) {
                return $this->raw_data[0];
            } elseif ($this->data !== null) {
                return strval($this->data);
            }
            return "";

        case "errors":
            return $this->errors ?? [];

        default:
            throw new \OutOfBoundsException(
                "Property '$name' does not exist on " . get_class($this) . ". " .
                "Available virtual properties: value, errors"
            );
    }
}
```

## Better Long-Term Solution
Add explicit getters instead of magic methods:

```php
/**
 * Get field value for rendering
 * @return string
 */
public function getValue()
{
    if ($this->raw_data) {
        return $this->raw_data[0];
    } elseif ($this->data !== null) {
        return strval($this->data);
    }
    return "";
}
```

Then in templates:
```php
// Instead of: $field->value
echo $field->getValue();

// Or keep __get() as convenience but with validation
```

## Testing Required

Add tests in `tests/fields/GenericFieldTest.php`:

```php
/**
 * @expectedException \OutOfBoundsException
 * @expectedExceptionMessage Property 'nonexistent' does not exist
 */
public function testAccessUndefinedProperty()
{
    $field = new StringField(['name' => 'test']);
    $x = $field->nonexistent;  // Should throw
}

/**
 * @expectedException \OutOfBoundsException
 * @expectedExceptionMessage Property 'dataa' does not exist
 */
public function testTypoInPropertyName()
{
    $field = new StringField(['name' => 'test', 'data' => 'foo']);
    $x = $field->dataa;  // Typo should throw
}

public function testValuePropertyStillWorks()
{
    $field = new StringField(['name' => 'test', 'data' => 'foo']);
    $this->assertEquals('foo', $field->value);  // Should still work
}
```

## Related Issues
Similar pattern exists in:
- `Form::__get()` (returns null for non-existent fields)
- `Flags::__get()` (auto-creates false flags)

Consider fixing all magic `__get()` methods consistently.

## Backward Compatibility
This is a **breaking change** if code relies on null returns. Migration:

1. Add deprecation warning first (Option 2)
2. Log warnings in production to find problem code
3. After one release cycle, switch to exceptions (Option 1)
