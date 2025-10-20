# HIGH-004: Loose Comparison in EqualTo Validator

## Priority
**HIGH** - Logic bug causing false positives

## Category
Bug - Type Safety

## Affected File
`src/validators/EqualTo.php` - Line 56

## Description
The `EqualTo` validator uses loose comparison (`!=`) instead of strict comparison (`!==`). This causes type-unsafe comparisons where `"0"` equals `0`, `false` equals `""`, etc.

## Problematic Code
```php
public function __invoke(Form $form, Field $field, $message = "")
{
    // ... get other field ...

    if ($field->data != $other->data) {  // LINE 56 - Loose comparison!
        $d = [
            "other_label" => $other->label->text,
            "other_name"  => $this->fieldname
        ];
        $message = vsprintf_named($this->message ?: "Field must be equal to %(other_name)s", $d);
        throw new ValidationError($message);
    }
}
```

## Problem Examples

### Example 1: String vs Integer
```php
$form->age = new IntegerField(['data' => 0]);
$form->age_confirm = new StringField([
    'data' => "0",
    'validators' => [new EqualTo(['fieldname' => 'age'])]
]);

$form->validate();  // PASSES (but types are different!)
// "0" == 0 is true (should be false with strict comparison)
```

### Example 2: False vs Empty String
```php
$form->accept_terms = new BooleanField(['data' => false]);
$form->accept_terms_confirm = new StringField([
    'data' => "",
    'validators' => [new EqualTo(['fieldname' => 'accept_terms'])]
]);

$form->validate();  // PASSES (but types are different!)
// false == "" is true (should be false)
```

### Example 3: Null vs Zero
```php
$form->score = new IntegerField(['data' => 0]);
$form->score_confirm = new IntegerField([
    'data' => null,
    'validators' => [new EqualTo(['fieldname' => 'score'])]
]);

$form->validate();  // PASSES (but values are different!)
// 0 == null is true (should be false)
```

## Impact
- Password confirmation fields accept type-mismatched values
- Email confirmation accepts integer `0` for string `"0"`
- Boolean confirmations accept empty strings
- Data integrity issues in form submissions

## Suggested Fix
Use strict comparison:

```php
if ($field->data !== $other->data) {  // FIX: Use strict comparison
    $d = [
        "other_label" => $other->label->text,
        "other_name"  => $this->fieldname
    ];
    $message = vsprintf_named($this->message ?: "Field must be equal to %(other_name)s", $d);
    throw new ValidationError($message);
}
```

## Testing Required
Add tests in `tests/validators/EqualToTest.php`:

```php
public function testStrictTypeComparison()
{
    $form = new Form();
    $form->field1 = new IntegerField(['data' => 0]);
    $form->field2 = new StringField([
        'data' => "0",
        'validators' => [new EqualTo('', ['fieldname' => 'field1'])]
    ]);

    // Should FAIL - types are different
    $this->assertFalse($form->validate());
}

public function testNullVsZeroComparison()
{
    $form = new Form();
    $form->field1 = new IntegerField(['data' => 0]);
    $form->field2 = new IntegerField([
        'data' => null,
        'validators' => [new EqualTo('', ['fieldname' => 'field1'])]
    ]);

    // Should FAIL - values are different
    $this->assertFalse($form->validate());
}

public function testFalseVsEmptyStringComparison()
{
    $form = new Form();
    $form->field1 = new BooleanField(['data' => false]);
    $form->field2 = new StringField([
        'data' => "",
        'validators' => [new EqualTo('', ['fieldname' => 'field1'])]
    ]);

    // Should FAIL - types are different
    $this->assertFalse($form->validate());
}
```

## Similar Issues
Check other validators for loose comparisons:
```bash
grep -n "!=" src/validators/*.php | grep -v "!=="
grep -n "==" src/validators/*.php | grep -v "==="
```
