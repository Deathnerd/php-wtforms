# MEDIUM-002: Missing Interface Contracts

## Priority
**MEDIUM** - Architecture/Maintainability

## Category
Architecture - Type Safety

## Affected Files
- All field classes (no `FieldInterface`)
- All validator classes (no `ValidatorInterface`)
- All widget classes (no `WidgetInterface`)

## Description
The library relies entirely on duck-typing with no explicit interface contracts. This prevents compile-time type checking, makes APIs unclear, and violates SOLID principles.

## Current State
```php
// No interface - just duck typing
class CustomValidator extends Validator {
    public function __invoke(Form $form, Field $field, $message = "") {
        // ...
    }
}

// No type hints possible
function processField(Field $field) {  // Could be any Field subclass
    $field->validate(...);  // No guarantee this method exists
}
```

## Problems

### 1. No Type Safety
```php
function renderWidget($widget, $field) {  // No type hints!
    return $widget($field);  // Hope $widget is callable...
}
```

### 2. Unclear API Contracts
Developers must read source code to understand what methods are required:
- What must a custom Field implement?
- What must a custom Validator provide?
- What must a custom Widget expose?

### 3. IDE Support Lacking
```php
$validator = $this->getValidator();
$validator->  // <-- IDE cannot autocomplete, no known interface
```

### 4. Runtime Errors Instead of Compile Errors
```php
class BrokenValidator extends Validator {
    // Forgot to implement __invoke()
}

$form->field->validators[] = new BrokenValidator();
$form->validate();  // CRASH at runtime (should fail at compile time)
```

## Suggested Fix

### Step 1: Define Interfaces

**FieldInterface.php**:
```php
<?php
namespace WTForms\Interfaces;

use WTForms\Form;

interface FieldInterface
{
    /**
     * Validate this field
     * @param Form $form The parent form
     * @param array $extra_validators Additional validators
     * @return bool True if valid
     */
    public function validate(Form $form, array $extra_validators = []): bool;

    /**
     * Process incoming data
     * @param array $formdata Form submission data
     * @param mixed $data Default/object data
     */
    public function process($formdata, $data = null): void;

    /**
     * Populate object property with field data
     * @param object $obj Target object
     * @param string $name Property name
     */
    public function populateObj($obj, $name): void;

    /**
     * Render the field as HTML
     * @param array $options Render options
     * @return string HTML output
     */
    public function __invoke(array $options = []): string;
}
```

**ValidatorInterface.php**:
```php
<?php
namespace WTForms\Interfaces;

use WTForms\Form;
use WTForms\Fields\Core\Field;

interface ValidatorInterface
{
    /**
     * Validate a field
     *
     * @param Form $form The parent form
     * @param Field $field The field being validated
     * @param string $message Optional error message override
     * @throws \WTForms\Exceptions\ValidationError If validation fails
     * @throws \WTForms\Exceptions\StopValidation To halt validation chain
     */
    public function __invoke(Form $form, Field $field, string $message = ""): void;
}
```

**WidgetInterface.php**:
```php
<?php
namespace WTForms\Interfaces;

use WTForms\Fields\Core\Field;

interface WidgetInterface
{
    /**
     * Render widget HTML
     *
     * @param Field $field The field to render
     * @param array $options Additional rendering options
     * @return string HTML output
     */
    public function __invoke(Field $field, array $options = []): string;
}
```

### Step 2: Update Base Classes

**Field.php**:
```php
abstract class Field implements FieldInterface, \Iterator
{
    // ... existing implementation
}
```

**Validator.php**:
```php
abstract class Validator implements ValidatorInterface
{
    public $message;
    public $field_flags = [];

    // Remove NotImplemented exceptions - enforce via abstract
    abstract public function __invoke(Form $form, Field $field, string $message = ""): void;
}
```

**Widget.php**:
```php
abstract class Widget implements WidgetInterface
{
    public $field_flags = [];

    // Remove NotImplemented exception - enforce via abstract
    abstract public function __invoke(Field $field, array $options = []): string;
}
```

### Step 3: Update Type Hints

**Field.php validation chain**:
```php
protected function runValidationChain(Form $form, array $validators): bool
{
    foreach ($validators as $v) {
        // Add type check
        if (!($v instanceof ValidatorInterface) && !is_callable($v)) {
            throw new TypeError("Validator must implement ValidatorInterface or be callable");
        }

        try {
            if (is_array($v)) {
                call_user_func($v, $form, $this);
            } else {
                $v->__invoke($form, $this);
            }
        } catch (StopValidation $e) {
            // ...
        }
    }
}
```

## Benefits

1. **Compile-time safety**: Type errors caught early
2. **Better IDE support**: Autocomplete and type checking
3. **Clear API contracts**: Documentation through interfaces
4. **Easier testing**: Mock interfaces instead of concrete classes
5. **SOLID compliance**: Dependency Inversion Principle

## Migration Path

This is **backward compatible** if done carefully:

1. Add interfaces
2. Make base classes implement interfaces
3. Existing subclasses automatically comply (structural typing)
4. Add type hints gradually (non-breaking)
5. Update documentation

## Testing Required

Verify all existing tests pass after adding interfaces:

```bash
./vendor/bin/phpunit
```

Add new tests for interface compliance:

```php
public function testFieldImplementsInterface()
{
    $field = new StringField(['name' => 'test']);
    $this->assertInstanceOf(FieldInterface::class, $field);
}

public function testValidatorImplementsInterface()
{
    $validator = new Email();
    $this->assertInstanceOf(ValidatorInterface::class, $validator);
}
```

## Impact
- Low risk (backward compatible)
- High value (improved code quality)
- Foundation for future improvements (PHP 7+ type hints)
