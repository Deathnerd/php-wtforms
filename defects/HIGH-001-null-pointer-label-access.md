# HIGH-001: Null Pointer Risk in Form Field Assignment

## Priority
**HIGH** - Runtime crash risk

## Category
Bug - Null Safety

## Affected File
`src/Form.php` - Line 194

## Description
When attaching a field to a form via `__set()`, the code accesses `$value->label->text` without checking if `label` is null. This can cause a fatal error if a field is constructed without a label.

## Vulnerable Code
```php
public function __set($name, $value)
{
    if ($value instanceof Field) {
        // ... earlier assignments ...

        if (!$value->label->text) {  // LINE 194 - No null check on label!
            $value->label = new Label($value->id,
                ucwords(str_replace("_", " ", $value->short_name)));
        }
        $this->fields[$name] = $value;
    }
    // ...
}
```

## Problem
If `$value->label` is `null`, accessing `->text` will cause:
```
Fatal error: Trying to get property 'text' of non-object
```

## Attack Scenario
```php
// Edge case: Field constructed without label initialization
class BrokenField extends Field {
    public function __construct(array $options = []) {
        // Doesn't call parent, label stays null
        $this->name = "broken";
    }
}

$form = new Form();
$form->broken = new BrokenField();  // CRASH!
```

## Impact
- Application crash
- Potential denial of service
- Poor error message for developers

## Suggested Fix
Add null check before property access:

```php
public function __set($name, $value)
{
    if ($value instanceof Field) {
        // ... earlier assignments ...

        if (!$value->label || !$value->label->text) {  // FIX: Check label exists
            $value->label = new Label($value->id,
                ucwords(str_replace("_", " ", $value->short_name)));
        }
        $this->fields[$name] = $value;
    }
    // ...
}
```

## Better Fix (Defensive)
Ensure label is always initialized in Field constructor:

```php
// In Field.php constructor, after line 192
if (!$this->label) {
    $this->label = new Label($this->id,
        ucwords(str_replace("_", " ", $this->short_name)));
}
```

## Testing Required
Add test in `tests/common/FormTest.php`:

```php
public function testFieldWithoutLabel()
{
    $form = new Form();
    $field = new StringField(['name' => 'test']);
    $field->label = null;  // Simulate broken field

    $form->test = $field;  // Should not crash

    $this->assertInstanceOf(Label::class, $form->test->label);
}
```
