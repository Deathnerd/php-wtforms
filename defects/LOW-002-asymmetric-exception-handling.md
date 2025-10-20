# LOW-002: Asymmetric Exception Handling in Validation

## Priority
**LOW** - Consistency issue

## Category
Bug - Exception Handling

## Affected File
`src/fields/core/Field.php` - Lines 281-305

## Description
The `validate()` method catches different exception types in `preValidate()` vs `postValidate()`. This asymmetry is confusing and potentially masks errors.

## Current Code
```php
public function validate(Form $form, array $extra_validators = [])
{
    $this->errors = $this->process_errors;
    $stop_validation = false;

    // Pre-validate catches 3 exception types
    try {
        $this->preValidate($form);
    } catch (StopValidation $e) {  // Caught
        $message = $e->getMessage();
        if ($message != "") {
            $this->errors[] = $message;
        }
        $stop_validation = true;
    } catch (ValueError $e) {  // Caught
        $this->errors[] = $e->getMessage();
    } catch (ValidationError $e) {  // Caught
        $this->errors[] = $e->getMessage();
    }

    // Validation chain
    if (!$stop_validation) {
        $stop_validation = $this->runValidationChain($form,
            array_merge($this->validators, $extra_validators));
    }

    // Post-validate catches only 1 exception type!
    try {
        $this->postValidate($form, $stop_validation);
    } catch (ValueError $e) {  // Only this one caught
        $this->errors[] = $e->getMessage();
    }
    // Missing: StopValidation, ValidationError

    return count($this->errors) == 0;
}
```

## Problem
If `postValidate()` throws `StopValidation` or `ValidationError`, it **propagates uncaught** instead of being added to `$this->errors`.

## Reproduction
```php
class StrictField extends StringField
{
    public function postValidate(Form $form, $stop_validation)
    {
        if ($this->data === "forbidden") {
            // This exception is NOT caught!
            throw new ValidationError("This value is forbidden in post-validation");
        }
    }
}

$form = new Form();
$form->field = new StrictField(['data' => 'forbidden']);

// Expected: validation fails gracefully with error message
// Actual: Uncaught exception crashes
$form->validate();  // CRASH!
```

## Impact
- Inconsistent exception handling
- Custom fields with postValidate hooks can crash unexpectedly
- Confusing for developers extending Field
- Violates principle of least surprise

## Suggested Fix
Make exception handling symmetric:

```php
public function validate(Form $form, array $extra_validators = [])
{
    $this->errors = $this->process_errors;
    $stop_validation = false;

    // Pre-validate
    try {
        $this->preValidate($form);
    } catch (StopValidation $e) {
        $message = $e->getMessage();
        if ($message != "") {
            $this->errors[] = $message;
        }
        $stop_validation = true;
    } catch (ValueError $e) {
        $this->errors[] = $e->getMessage();
    } catch (ValidationError $e) {
        $this->errors[] = $e->getMessage();
    }

    // Validation chain
    if (!$stop_validation) {
        $stop_validation = $this->runValidationChain($form,
            array_merge($this->validators, $extra_validators));
    }

    // Post-validate - NOW SYMMETRIC
    try {
        $this->postValidate($form, $stop_validation);
    } catch (StopValidation $e) {  // ADD: Catch StopValidation
        $message = $e->getMessage();
        if ($message != "") {
            $this->errors[] = $message;
        }
        // Don't set $stop_validation - already done
    } catch (ValueError $e) {
        $this->errors[] = $e->getMessage();
    } catch (ValidationError $e) {  // ADD: Catch ValidationError
        $this->errors[] = $e->getMessage();
    }

    return count($this->errors) == 0;
}
```

## Alternative: Unified Handler
Refactor to reduce duplication:

```php
private function handleValidationException(\Exception $e)
{
    if ($e instanceof StopValidation) {
        $message = $e->getMessage();
        if ($message != "") {
            $this->errors[] = $message;
        }
        return true;  // Stop validation
    } elseif ($e instanceof ValueError || $e instanceof ValidationError) {
        $this->errors[] = $e->getMessage();
        return false;  // Continue validation
    }
    throw $e;  // Re-throw unexpected exceptions
}

public function validate(Form $form, array $extra_validators = [])
{
    $this->errors = $this->process_errors;
    $stop_validation = false;

    // Pre-validate
    try {
        $this->preValidate($form);
    } catch (\Exception $e) {
        $stop = $this->handleValidationException($e);
        if ($stop) {
            $stop_validation = true;
        }
    }

    // Validation chain
    if (!$stop_validation) {
        $stop_validation = $this->runValidationChain($form,
            array_merge($this->validators, $extra_validators));
    }

    // Post-validate
    try {
        $this->postValidate($form, $stop_validation);
    } catch (\Exception $e) {
        $this->handleValidationException($e);
    }

    return count($this->errors) == 0;
}
```

## Testing Required
Add test in `tests/fields/PrePostValidationTest.php`:

```php
public function testPostValidateStopValidation()
{
    $field = new class(['name' => 'test']) extends StringField {
        public function postValidate(Form $form, $stop_validation) {
            throw new StopValidation("Post-validation stopped");
        }
    };

    $form = new Form();
    $form->test = $field;
    $form->process(['test' => 'value']);

    // Should not crash
    $this->assertFalse($form->validate());
    $this->assertEquals(['Post-validation stopped'], $form->test->errors);
}

public function testPostValidateValidationError()
{
    $field = new class(['name' => 'test']) extends StringField {
        public function postValidate(Form $form, $stop_validation) {
            throw new ValidationError("Post-validation failed");
        }
    };

    $form = new Form();
    $form->test = $field;
    $form->process(['test' => 'value']);

    // Should not crash
    $this->assertFalse($form->validate());
    $this->assertEquals(['Post-validation failed'], $form->test->errors);
}
```

## Documentation
Update docblock for `postValidate()`:

```php
/**
 * Override if you need to run any field-level validation tasks after
 * normal validation. This shouldn't be needed in most cases.
 *
 * Can throw ValidationError or StopValidation to add errors.
 * ValueError is also caught and converted to error message.
 *
 * @param Form    $form            The form the field belongs to
 * @param boolean $stop_validation True if any validator raised StopValidation
 * @throws ValidationError To add validation error
 * @throws StopValidation To add error and stop (though validation already stopped)
 * @throws ValueError To add error from value processing
 */
public function postValidate(Form $form, $stop_validation)
{
}
```

## Benefits
- Consistent behavior
- Fewer surprises for developers
- Better error handling
- More robust validation lifecycle
