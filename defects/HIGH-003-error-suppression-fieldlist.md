# HIGH-003: Error Suppression Anti-Pattern in FieldList

## Priority
**HIGH** - Code quality issue masking potential bugs

## Category
Bug - Error Handling

## Affected File
`src/fields/core/FieldList.php` - Line 308

## Description
The `FieldList::populateObj()` method uses the error suppression operator `@` to silence undefined array key warnings. This is an anti-pattern that masks bugs and makes debugging difficult.

## Problematic Code
```php
public function populateObj($obj, $name)
{
    $values = $obj->$name;
    if (!is_array($values)) {
        $values = [];
    }
    $output = [];
    $i = 0;
    foreach ($this->entries as $field) {
        $fake_obj = new \stdClass();
        $fake_obj->data = @$values[$i];  // LINE 308 - Error suppression!
        $field->populateObj($fake_obj, 'data');
        $output[] = $fake_obj->data;
        $i++;
    }
    $obj->$name = $output;
}
```

## Problem
The `@` operator suppresses all errors, including:
- Undefined array key warnings (intended)
- Type errors if `$values` is somehow not array-like (unintended)
- Other unexpected errors that should be caught during development

## Why This Is Bad
1. **Masks bugs**: If `$values` becomes corrupted or has unexpected structure, errors are hidden
2. **Poor performance**: Error suppression has a small performance cost
3. **Hard to debug**: Developers cannot see warnings during development
4. **Modern PHP deprecation**: PHP 8+ makes more notices into warnings/errors

## Suggested Fix
Use proper bounds checking:

```php
public function populateObj($obj, $name)
{
    $values = $obj->$name;
    if (!is_array($values)) {
        $values = [];
    }
    $output = [];
    $i = 0;
    foreach ($this->entries as $field) {
        $fake_obj = new \stdClass();
        $fake_obj->data = array_key_exists($i, $values) ? $values[$i] : null;
        $field->populateObj($fake_obj, 'data');
        $output[] = $fake_obj->data;
        $i++;
    }
    $obj->$name = $output;
}
```

## Alternative Fix (PHP 7.0+)
Use null coalescing operator:

```php
$fake_obj->data = $values[$i] ?? null;
```

## Impact
- Better error visibility during development
- Clearer code intent
- Easier debugging
- Better PHP 8+ compatibility

## Testing Required
Verify existing tests still pass, then add edge case:

```php
public function testPopulateObjWithMismatchedSizes()
{
    // FieldList with 5 entries
    $field_list = new FieldList(/* ... */);
    for ($i = 0; $i < 5; $i++) {
        $field_list->appendEntry();
    }

    // Object with only 3 values
    $obj = new \stdClass();
    $obj->items = ['a', 'b', 'c'];

    // Process with all 5 entries
    $field_list->process(['formdata' => [
        'items-0' => 'x',
        'items-1' => 'y',
        'items-2' => 'z',
        'items-3' => 'w',
        'items-4' => 'v',
    ]]);

    // Populate back to object (should handle size mismatch gracefully)
    $field_list->populateObj($obj, 'items');

    $this->assertCount(5, $obj->items);
}
```

## Related Issues
Similar pattern might exist elsewhere. Search for `@$` throughout codebase:
```bash
grep -r "@\$" src/
```
