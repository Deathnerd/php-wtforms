# TEST-001: Missing @dataProvider Usage

## Priority
**LOW** - Code quality / Test maintainability

## Category
Testing - Test Structure

## Affected Files
All test files, particularly:
- `tests/validators/*.php` (16 files)
- `tests/fields/*.php` (18 files)

## Description
The test suite does not use PHPUnit's `@dataProvider` annotation, leading to repetitive test code and reduced maintainability. Many validators and fields have nearly identical test methods with different input values.

## Current Pattern (Repetitive)

### Example 1: EmailTest.php
```php
public function testEmail()
{
    $email = new Email();
    $this->assertNull($email($this->form, new DummyField(["data" => 'foo@bar.dk'])));
    $this->assertNull($email($this->form, new DummyField(["data" => "123@bar.dk"])));
    $this->assertNull($email($this->form, new DummyField(["data" => "foo@456.dk"])));
    $this->assertNull($email($this->form, new DummyField(["data" => "foo@bar456.dk"])));
    $this->assertNull($email($this->form, new DummyField(["data" => "foo@bar.456"])));
}

/**
 * @expectedException \WTForms\Exceptions\ValidationError
 */
public function testNull() {
    (new Email)->__invoke($this->form, new DummyField(["data" => null]));
}

/**
 * @expectedException \WTForms\Exceptions\ValidationError
 */
public function testNullString() {
    (new Email)->__invoke($this->form, new DummyField(["data" => ""]));
}

/**
 * @expectedException \WTForms\Exceptions\ValidationError
 */
public function testWhitespace() {
    (new Email)->__invoke($this->form, new DummyField(["data" => "   "]));
}

// ... many more similar methods
```

**Issues:**
- 9 separate test methods for similar scenarios
- Hard to see all test cases at once
- Difficult to add new test cases
- Lots of boilerplate

## Improved Pattern (Using @dataProvider)

### Refactored EmailTest.php
```php
/**
 * @dataProvider validEmailProvider
 */
public function testValidEmails($email)
{
    $validator = new Email();
    $field = new DummyField(["data" => $email]);

    // Should not throw
    $this->assertNull($validator($this->form, $field));
}

public function validEmailProvider()
{
    return [
        'simple email' => ['foo@bar.dk'],
        'numeric local' => ['123@bar.dk'],
        'numeric domain' => ['foo@456.dk'],
        'alphanumeric domain' => ['foo@bar456.dk'],
        'numeric TLD' => ['foo@bar.456'],
        'subdomain' => ['foo@mail.bar.com'],
        'plus addressing' => ['foo+tag@bar.com'],
        'hyphen in domain' => ['foo@my-site.com'],
    ];
}

/**
 * @dataProvider invalidEmailProvider
 * @expectedException \WTForms\Exceptions\ValidationError
 */
public function testInvalidEmails($email)
{
    $validator = new Email();
    $field = new DummyField(["data" => $email]);
    $validator($this->form, $field);
}

public function invalidEmailProvider()
{
    return [
        'null' => [null],
        'empty string' => [''],
        'whitespace only' => ['   '],
        'no at sign' => ['foobar.dk'],
        'no domain' => ['foo@'],
        'no local part' => ['@bar.dk'],
        'double at' => ['foo@@bar.dk'],
        'spaces' => ['foo bar@test.com'],
        'missing TLD' => ['foo@bar'],
    ];
}
```

**Benefits:**
- 2 test methods instead of 9+
- All test cases visible in provider
- Easy to add new cases (just add array entry)
- Descriptive keys explain each case
- ~70% less code

## Impact of Not Using Data Providers

### Current State
```bash
$ find tests/ -name "*Test.php" | wc -l
53 test files

$ grep -r "public function test" tests/ | wc -l
~240 test methods

$ wc -l tests/**/*.php
~4,327 lines of test code
```

### Estimated Improvement
With `@dataProvider`:
- ~35% reduction in test code lines (~1,500 lines saved)
- ~40% reduction in test methods (~100 fewer methods)
- Much easier to add test cases
- Better test documentation

## Suggested Refactoring Priority

### High Priority (Most Benefit)
1. **Validator Tests** - Most repetitive
   - EmailTest.php
   - LengthTest.php
   - NumberRangeTest.php
   - URLTest.php
   - IPAddressTest.php
   - UUIDTest.php

2. **Field Processing Tests**
   - IntegerFieldTest.php
   - FloatFieldTest.php
   - BooleanFieldTest.php
   - StringFieldTest.php

### Medium Priority
3. **Widget Rendering Tests**
   - HTML5FieldsTest.php
   - BasicWidgetsTest.php

### Low Priority
4. **Complex Integration Tests**
   - FormTest.php
   - FieldListTest.php
   - FormFieldTest.php

## Example Refactoring

### Before: LengthTest.php
```php
public function testLengthMinimumFails() { /* ... */ }
public function testLengthMinimumSucceeds() { /* ... */ }
public function testLengthMaximumFails() { /* ... */ }
public function testLengthMaximumSucceeds() { /* ... */ }
public function testLengthBothFails() { /* ... */ }
public function testLengthBothSucceeds() { /* ... */ }
// 6 separate methods
```

### After: LengthTest.php
```php
/**
 * @dataProvider lengthValidationProvider
 */
public function testLengthValidation($value, $min, $max, $shouldPass)
{
    $validator = new Length('', ['min' => $min, 'max' => $max]);
    $field = new DummyField(['data' => $value]);

    if ($shouldPass) {
        $this->assertNull($validator($this->form, $field));
    } else {
        $this->expectException(ValidationError::class);
        $validator($this->form, $field);
    }
}

public function lengthValidationProvider()
{
    return [
        'too short min only' => ['ab', 3, -1, false],
        'exact min only' => ['abc', 3, -1, true],
        'longer min only' => ['abcd', 3, -1, true],
        'too long max only' => ['abcdef', -1, 5, false],
        'exact max only' => ['abcde', -1, 5, true],
        'shorter max only' => ['abcd', -1, 5, true],
        'too short with range' => ['ab', 3, 5, false],
        'too long with range' => ['abcdef', 3, 5, false],
        'in range min' => ['abc', 3, 5, true],
        'in range max' => ['abcde', 3, 5, true],
        'in range middle' => ['abcd', 3, 5, true],
    ];
}
// 1 method handles all cases
```

## Implementation Plan

1. **Phase 1**: Refactor validator tests (highest impact)
   - Start with EmailTest.php as template
   - Apply pattern to other validators
   - Verify all tests still pass

2. **Phase 2**: Refactor field tests
   - Focus on numeric fields first (IntegerField, FloatField)
   - Then string/boolean fields

3. **Phase 3**: Refactor widget tests
   - HTML rendering tests benefit from parameterization

4. **Phase 4**: Document pattern
   - Add to CLAUDE.md
   - Create example test for contributors

## Testing the Refactoring
After each refactoring:

```bash
# Verify tests still pass
./vendor/bin/phpunit

# Check coverage didn't decrease
./vendor/bin/phpunit --coverage-html tests/_reports/coverage

# Verify test count is reasonable
./vendor/bin/phpunit --list-tests | wc -l
```

## Documentation
Add to CLAUDE.md:

```markdown
## Testing Guidelines

### Use @dataProvider for Parameterized Tests

When testing multiple inputs with same logic, use data providers:

\`\`\`php
/**
 * @dataProvider validInputProvider
 */
public function testValidInputs($input) {
    // Single test method handles all cases
}

public function validInputProvider() {
    return [
        'case 1' => ['input1'],
        'case 2' => ['input2'],
    ];
}
\`\`\`

Benefits:
- Less code duplication
- Easier to add test cases
- Better failure reporting (shows which data set failed)
```

## Benefits Summary
- **Maintainability**: 35% less test code
- **Readability**: All cases visible in one place
- **Extensibility**: Adding new test case = adding array entry
- **Debugging**: PHPUnit reports which dataset failed
- **Documentation**: Test cases serve as specification
