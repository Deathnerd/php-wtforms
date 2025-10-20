# HIGH-005: Missing Field Existence Validation in EqualTo

## Priority
**HIGH** - Runtime crash risk

## Category
Bug - Input Validation

## Affected File
`src/validators/EqualTo.php` - Line 49

## Description
The `EqualTo` validator accesses a form field without checking if it actually exists. If the referenced field name is invalid, a fatal error occurs.

## Vulnerable Code
```php
public function __invoke(Form $form, Field $field, $message = "")
{
    if ($form->{$this->fieldname} !== null) {
        $other = $form->{$this->fieldname};
    } else {
        throw new ValidationError("Invalid field name '{$this->fieldname}'");
    }
    // ...
}
```

## Problem
The check `$form->{$this->fieldname} !== null` only verifies the field's value is not null, not that the field exists. If the field doesn't exist, `$form->__get()` returns `null`, and the error message is misleading.

## Reproduction
```php
$form = new Form();
$form->password = new PasswordField();
$form->password_confirm = new PasswordField([
    'validators' => [
        new EqualTo('', ['fieldname' => 'pasword'])  // Typo: "pasword" not "password"
    ]
]);

$form->process(['password' => 'secret123', 'password_confirm' => 'secret123']);
$form->validate();

// Error: "Invalid field name 'pasword'"
// But real issue is: field doesn't exist, not that it's invalid
```

## Worse Case
```php
$form->field1 = new StringField(['data' => null]);
$form->field2 = new StringField([
    'validators' => [new EqualTo('', ['fieldname' => 'field1'])]
]);

// field1 exists but is null
// Throws "Invalid field name 'field1'" even though field EXISTS
```

## Impact
- Confusing error messages for typos
- False errors when comparing against nullable fields
- Difficult debugging for developers
- Poor user experience

## Suggested Fix
Check field existence properly:

```php
public function __invoke(Form $form, Field $field, $message = "")
{
    if (!array_key_exists($this->fieldname, $form->fields)) {
        throw new TypeError("EqualTo validator references non-existent field '{$this->fieldname}'");
    }

    $other = $form->{$this->fieldname};

    if ($field->data !== $other->data) {  // Also fix loose comparison (see HIGH-004)
        $d = [
            "other_label" => $other->label->text,
            "other_name"  => $this->fieldname
        ];
        $message = vsprintf_named(
            $this->message ?: "Field must be equal to %(other_name)s",
            $d
        );
        throw new ValidationError($message);
    }
}
```

## Better Approach (Fail Early)
Validate field name in constructor:

```php
public function __construct($message = "", array $options = [])
{
    if (!array_key_exists('fieldname', $options)) {
        throw new TypeError("EqualTo validator requires 'fieldname' option");
    }

    if (!is_string($options['fieldname']) || $options['fieldname'] === '') {
        throw new TypeError("EqualTo 'fieldname' must be a non-empty string");
    }

    $this->fieldname = $options['fieldname'];
    $this->message = $message;
}
```

Then in `__invoke()`, just check existence:

```php
if (!array_key_exists($this->fieldname, $form->fields)) {
    throw new TypeError("Field '{$this->fieldname}' does not exist on form");
}
```

## Testing Required
Add tests in `tests/validators/EqualToTest.php`:

```php
/**
 * @expectedException \WTForms\Exceptions\TypeError
 * @expectedExceptionMessage does not exist on form
 */
public function testNonExistentField()
{
    $form = new Form();
    $form->field1 = new StringField(['data' => 'foo']);
    $form->field2 = new StringField([
        'data' => 'foo',
        'validators' => [new EqualTo('', ['fieldname' => 'nonexistent'])]
    ]);

    $form->validate();  // Should throw TypeError, not ValidationError
}

public function testFieldWithNullValue()
{
    $form = new Form();
    $form->field1 = new StringField(['data' => null]);
    $form->field2 = new StringField([
        'data' => null,
        'validators' => [new EqualTo('', ['fieldname' => 'field1'])]
    ]);

    // Should validate successfully (both null)
    $this->assertTrue($form->validate());
}
```
