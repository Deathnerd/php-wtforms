# HIGH-002: Array Bounds Error in Optional Validator

## Priority
**HIGH** - Runtime crash risk

## Category
Bug - Bounds Checking

## Affected File
`src/validators/Optional.php` - Line 61

## Description
The `Optional` validator accesses `$field->raw_data[0]` without checking if the array is non-empty. This causes a fatal error when `raw_data` is an empty array.

## Vulnerable Code
```php
public function __invoke(Form $form, Field $field, $message = "")
{
    if (is_null($field->raw_data)
        || (is_string($field->raw_data[0]) && $this->stringCheck($field->raw_data[0]) == "")
        //             ^^^^^^^^^^^^^^^^^ No bounds check!
    ) {
        $field->errors = [];
        throw new StopValidation();
    }
}
```

## Problem
If `$field->raw_data` is an empty array `[]`, accessing index `[0]` causes:
```
Notice: Undefined offset: 0
```

Or in PHP 8+:
```
Warning: Undefined array key 0
```

## Reproduction
```php
$form = new Form();
$form->field = new StringField([
    'validators' => [new Optional()]
]);

// Simulate empty submission (field not in POST data)
$form->process(['formdata' => []]);

// raw_data is set to [] in Field::process() line 416
// Optional validator crashes trying to access [0]
$form->validate();  // ERROR!
```

## Impact
- Application crash on empty form submissions
- Poor handling of optional fields
- User-facing errors during validation

## Suggested Fix
Check array bounds before access:

```php
public function __invoke(Form $form, Field $field, $message = "")
{
    if (is_null($field->raw_data)
        || count($field->raw_data) === 0  // FIX: Check array is non-empty
        || (is_string($field->raw_data[0]) && $this->stringCheck($field->raw_data[0]) == "")
    ) {
        $field->errors = [];
        throw new StopValidation();
    }
}
```

## Alternative Fix (More Defensive)
```php
public function __invoke(Form $form, Field $field, $message = "")
{
    $is_empty = is_null($field->raw_data)
        || count($field->raw_data) === 0;

    if (!$is_empty && count($field->raw_data) > 0) {
        $first_value = $field->raw_data[0];
        $is_empty = is_string($first_value) && $this->stringCheck($first_value) == "";
    }

    if ($is_empty) {
        $field->errors = [];
        throw new StopValidation();
    }
}
```

## Testing Required
Add test in `tests/validators/OptionalTest.php`:

```php
public function testOptionalWithEmptyRawData()
{
    $form = new Form();
    $form->field = new StringField([
        'validators' => [new Optional()]
    ]);

    // Simulate field not present in POST
    $form->process(['formdata' => []]);

    // Should not crash
    $this->assertTrue($form->validate());
}

public function testOptionalWithEmptyStringInRawData()
{
    $form = new Form();
    $form->field = new StringField([
        'validators' => [new Optional()]
    ]);

    $form->process(['formdata' => ['field' => ['']]]);

    // Should stop validation without errors
    $this->assertTrue($form->validate());
}
```
