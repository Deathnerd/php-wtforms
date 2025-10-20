# CRITICAL-003: Filter Chaining Does Not Work

## Priority
**CRITICAL** - Core functionality broken

## Category
Bug - Data Processing

## Affected File
`src/fields/core/Field.php` - Line 427

## Description
The filter processing loop applies all filters to the **original data** instead of chaining them sequentially. This means filters do not compose, and only the last filter's result is used.

## Buggy Code
```php
public function process($formdata, $data = null)
{
    // ... earlier code ...

    try {
        foreach ($this->filters as $filter) {
            /** @var $filter callable */
            $this->data = $filter($data);  // BUG: Always uses original $data
        }
    } catch (ValueError $e) {
        $this->process_errors[] = $e->getMessage();
    }
}
```

## Problem
Each filter receives `$data` (the original input) instead of `$this->data` (the result of the previous filter). This breaks filter composition.

## Example Demonstrating Bug
```php
$field = new StringField([
    'filters' => [
        function($x) { return trim($x); },      // Remove whitespace
        function($x) { return strtoupper($x); }, // Uppercase
        function($x) { return substr($x, 0, 5); } // Take first 5 chars
    ]
]);

$field->process([], "  hello world  ");

// Expected: "HELLO" (trimmed, uppercased, truncated)
// Actual: "  hel" (only last filter applied to original input)
```

## Impact
- Filters cannot be composed
- Only the last filter has any effect
- Data transformations are broken
- Existing code relying on filter chains is silently broken

## Suggested Fix
Change line 427 to use `$this->data`:

```php
try {
    foreach ($this->filters as $filter) {
        /** @var $filter callable */
        $this->data = $filter($this->data);  // FIX: Chain filters
    }
} catch (ValueError $e) {
    $this->process_errors[] = $e->getMessage();
}
```

## Testing Required
The existing test in `tests/fields/FiltersTest.php` should be enhanced:

```php
public function testFilterChaining()
{
    $field = new StringField([
        'filters' => [
            function($x) { return trim($x); },
            function($x) { return strtoupper($x); },
            function($x) { return substr($x, 0, 5); },
        ]
    ]);

    $field->process([], "  hello world  ");

    // Should chain: "  hello world  " -> "hello world" -> "HELLO WORLD" -> "HELLO"
    $this->assertEquals("HELLO", $field->data);
}
```

## Why This Wasn't Caught
The existing `FiltersTest.php` tests filters individually but not their composition. The test uses:
```php
"filters" => [
    function ($x) { return trim($x); },
    function ($x) { return intval($x); },
    function ($x) { return negate($x); }
]
```

With input `" 2"`:
- Bug behavior: `negate(" 2")` = `negate(2)` = `-2` (works by accident)
- Correct behavior: `negate(intval(trim(" 2")))` = `-2` (same result)

The test passes accidentally because the final filter happens to work on the original input.
