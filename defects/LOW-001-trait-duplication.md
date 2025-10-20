# LOW-001: Duplicate Iterator Traits

## Priority
**LOW** - Code quality / DRY principle

## Category
Refactoring - Code Duplication

## Affected Files
- `src/FormIterator.php` (64 lines)
- `src/fields/core/FieldIterator.php` (77 lines)

## Description
`FormIterator` and `FieldIterator` are nearly identical traits that implement PHP's `Iterator` interface. The only difference is the property they iterate over (`$this->fields` vs `$this->entries`).

## FormIterator.php
```php
trait FormIterator
{
    public function current() {
        return current($this->fields);
    }

    public function next() {
        next($this->fields);
    }

    public function key() {
        return key($this->fields);
    }

    public function rewind() {
        reset($this->fields);
    }

    public function valid() {
        $key = key($this->fields);
        return ($key !== null && $key !== false);
    }
}
```

## FieldIterator.php
```php
trait FieldIterator
{
    public function current() {
        return current($this->entries);  // Only difference: entries vs fields
    }

    public function next() {
        return next($this->entries);
    }

    public function key() {
        return key($this->entries);
    }

    public function rewind() {
        reset($this->entries);
    }

    public function valid() {
        $key = key($this->entries);
        return ($key !== null && $key !== false);
    }
}
```

## Impact
- Code duplication (DRY violation)
- Bug fixes must be applied twice
- Maintenance burden
- Inconsistent behavior if one trait is updated but not the other

## Suggested Fix

### Option 1: Abstract Trait with Property Accessor (PHP 5.4+)
```php
<?php
namespace WTForms;

trait ArrayIterator
{
    /**
     * Override this method to specify which property to iterate
     * @return array
     */
    abstract protected function getIterableArray();

    public function current()
    {
        return current($this->getIterableArray());
    }

    public function next()
    {
        next($this->getIterableArray());
    }

    public function key()
    {
        return key($this->getIterableArray());
    }

    public function rewind()
    {
        reset($this->getIterableArray());
    }

    public function valid()
    {
        $key = key($this->getIterableArray());
        return ($key !== null && $key !== false);
    }
}
```

Usage in `Form`:
```php
class Form implements \ArrayAccess, \Iterator
{
    use ArrayIterator;

    protected function getIterableArray()
    {
        return $this->fields;
    }
}
```

Usage in `FieldList`:
```php
class FieldList extends Field implements \Iterator
{
    use ArrayIterator;

    protected function getIterableArray()
    {
        return $this->entries;
    }
}
```

### Option 2: Internal Iterator Property (Simpler)
```php
<?php
namespace WTForms;

trait ArrayIterator
{
    /**
     * Property name to iterate over
     * @var string
     */
    protected $iteratorProperty = 'fields';

    public function current()
    {
        $prop = $this->iteratorProperty;
        return current($this->$prop);
    }

    public function next()
    {
        $prop = $this->iteratorProperty;
        next($this->$prop);
    }

    public function key()
    {
        $prop = $this->iteratorProperty;
        return key($this->$prop);
    }

    public function rewind()
    {
        $prop = $this->iteratorProperty;
        reset($this->$prop);
    }

    public function valid()
    {
        $prop = $this->iteratorProperty;
        $key = key($this->$prop);
        return ($key !== null && $key !== false);
    }
}
```

Usage:
```php
class Form implements \ArrayAccess, \Iterator
{
    use ArrayIterator;
    protected $iteratorProperty = 'fields';
}

class FieldList extends Field implements \Iterator
{
    use ArrayIterator;
    protected $iteratorProperty = 'entries';
}
```

### Option 3: Composition Over Inheritance (Most Flexible)
```php
<?php
namespace WTForms;

class ArrayIteratorWrapper implements \Iterator
{
    private $array;

    public function __construct(array &$array)
    {
        $this->array = &$array;
    }

    public function current() { return current($this->array); }
    public function next() { next($this->array); }
    public function key() { return key($this->array); }
    public function rewind() { reset($this->array); }
    public function valid() {
        $key = key($this->array);
        return ($key !== null && $key !== false);
    }
}
```

Usage:
```php
class Form implements \Iterator
{
    private $iterator;

    public function __construct()
    {
        $this->iterator = new ArrayIteratorWrapper($this->fields);
    }

    // Delegate to iterator
    public function current() { return $this->iterator->current(); }
    public function next() { $this->iterator->next(); }
    public function key() { return $this->iterator->key(); }
    public function rewind() { $this->iterator->rewind(); }
    public function valid() { return $this->iterator->valid(); }
}
```

## Recommended Approach
**Option 1** (abstract trait) - Best balance of:
- DRY principle
- Type safety
- Clear intent
- PHP 5.4+ compatible

## Testing Required
All existing iterator tests must pass:

```bash
./vendor/bin/phpunit tests/common/FormTest.php --filter Iterator
./vendor/bin/phpunit tests/fields/FieldListTest.php --filter Iterator
```

Add test to verify both implementations still work:

```php
public function testFormIteratorWorks()
{
    $form = new TestForm();
    $form->a = new StringField(['name' => 'a']);
    $form->b = new StringField(['name' => 'b']);

    $keys = [];
    foreach ($form as $key => $field) {
        $keys[] = $key;
    }

    $this->assertEquals(['a', 'b'], $keys);
}

public function testFieldListIteratorWorks()
{
    $field_list = new FieldList([/* ... */]);
    $field_list->appendEntry();
    $field_list->appendEntry();

    $count = 0;
    foreach ($field_list as $entry) {
        $count++;
    }

    $this->assertEquals(2, $count);
}
```

## Migration Path
1. Create new `ArrayIterator` trait
2. Update `Form` to use new trait
3. Run tests
4. Update `FieldList` to use new trait
5. Run tests
6. Delete old `FormIterator` and `FieldIterator` traits
7. Update documentation

## Benefits
- Single source of truth for iteration logic
- Easier to maintain
- Easier to fix bugs (fix once, not twice)
- Reduced code size (~80 lines → ~40 lines)
