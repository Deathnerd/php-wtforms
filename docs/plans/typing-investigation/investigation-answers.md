# Investigation Answers
## PHP-WTForms Comprehensive Typing Initiative

**Investigation Started:** [TIMESTAMP]
**Last Updated:** [TIMESTAMP]
**Current Section:** [SECTION NUMBER]
**Status:** IN_PROGRESS | BLOCKED | COMPLETE

---

## How to Use This Document

This document captures answers to all investigation questions from the PRD. For each question:

1. **Read the question** carefully
2. **Investigate** using appropriate methods (code inspection, commands, documentation)
3. **Document your findings** with evidence (command output, code snippets, links)
4. **Mark the question complete** by checking the box in the PRD
5. **Note any follow-up questions** that arise during investigation

### Answer Format

```
### Q[X.Y] - [Brief Question Summary]

**Status:** Answered | Partial | Blocked | Needs Investigation

**Investigation Method:**
- [ ] Code inspection
- [ ] Command execution
- [ ] Documentation review
- [ ] Web search

**Findings:**
[Your detailed findings here]

**Evidence:**
```
[Command output, code snippets, or other evidence]
```

**Decision/Recommendation:**
[What we should do based on findings]

**Follow-up Questions:**
- [Any new questions that arose]
```

---

## Section 1: Current State Assessment

### Q1.1 - PHPStan Configuration

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (`phpstan.neon`, `phpstan.neon.dist`)
- [ ] Command execution
- [ ] Documentation review

**Findings:**
PHPStan is configured at level 5 with the following settings:
- **Level:** 5 (medium strictness)
- **Paths analyzed:** `src/` only (tests not included)
- **Excluded:** `vendor/`
- **Custom extension:** Includes `phpstan/extension.neon` which registers a `ValidatorThrowTypeExtension`
- **Exception handling:** Unchecked exceptions configured for `StopValidation` and `ValidationError`
- **treatPhpDocTypesAsCertain:** Commented out (defaults to true)

**Evidence:**
```neon
# phpstan.neon
parameters:
    level: 5
    paths:
        - src
    excludePaths:
        - vendor
    exceptions:
        uncheckedExceptionClasses:
            - WTForms\Exceptions\StopValidation
            - WTForms\Exceptions\ValidationError
includes:
    - phpstan/extension.neon

# phpstan/extension.neon
services:
  - class: WTForms\PHPStan\Extensions\ValidatorThrowTypeExtension
    tags:
      - phpstan.dynamicMethodThrowTypeExtension
```

**Decision/Recommendation:**
Current configuration is a reasonable starting point. To reach level 9:
1. Incrementally increase level (5 → 6 → 7 → 8 → 9)
2. Consider adding `tests/` to paths for full coverage
3. May need additional extensions for generics and advanced features

**Follow-up Questions:**
- What does ValidatorThrowTypeExtension do exactly?
- Should tests be included in analysis?

---

### Q1.2 - PHPStan Error Counts by Level

**Status:** Answered

**Investigation Method:**
- [x] Command execution (PHPStan at levels 5-9)

**Findings:**
Error counts increase significantly at each level:

| Level | Error Count | Key New Checks |
|-------|-------------|----------------|
| 5     | 8           | Current baseline - always-true checks, covariance issues |
| 6     | 118         | +110 - Missing generic type specifications |
| 7     | 158         | +40 - Union type checking |
| 8     | 179         | +21 - Report possibly undefined variables |
| 9     | 217         | +38 - Full mixed type checking |

**Evidence:**
```bash
# Level 5 (8 errors)
docker-compose run --rm php74 vendor/bin/phpstan analyse -l 5
# Errors: instanceof.alwaysTrue, function.impossibleType, deadCode.unreachable, argument.type (covariance)

# Level 6 (118 errors) - main jump
# Many "missingType.generics" errors like:
# Class WTForms\Widgets\HTML5\URLInput extends generic class WTForms\Widgets\Core\Input but does not specify its types: TData

# Level 7-9 add incrementally more strictness
```

**Decision/Recommendation:**
1. **Immediate goal:** Fix level 5 errors (8 errors) - mostly dead code and covariance issues
2. **Short-term:** Move to level 6 by adding generic type specifications to all classes extending generic base classes
3. **Medium-term:** Progressively work toward level 9

The jump from level 5 to 6 is the biggest (8 → 118), primarily due to generic type requirements.

**Follow-up Questions:**
- How do we properly specify generic types for all extending classes?
- What is the covariance fix for template types?

---

### Q1.3 - PHPStan Baseline Status

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (`phpstan-baseline.neon`)
- [ ] Command execution

**Findings:**
**No baseline file exists.** The file `phpstan-baseline.neon` does not exist in the project root.

Git history shows the baseline was recently removed (commit `da2548e Remove phpstan baseline`), indicating a conscious decision to work without a baseline.

**Evidence:**
```bash
$ ls -la phpstan-baseline.neon
# No baseline file exists

$ git log --oneline | grep baseline
# da2548e Remove phpstan baseline
```

**Decision/Recommendation:**
Keep the no-baseline approach. Instead of ignoring errors:
1. Fix all level 5 errors (currently 8)
2. Progressively move up levels
3. Only use baseline temporarily during level transitions if needed

**Follow-up Questions:**
- None - the baseline approach is clear (don't use one)

---

### Q1.4 - Native Return Type Coverage

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (sample 10 files)
- [x] Command execution (grep patterns)

**Findings:**
**Overall:** 132 out of 257 methods have native return types = **51% coverage**

**Sampled key files:**
| File | With Types | Total | Coverage |
|------|-----------|-------|----------|
| Field.php | 11 | 15 | 73% |
| Validator.php | 9 | 12 | 75% |
| Form.php | 6 | 13 | 46% |

Core classes (Field, Validator) have better coverage (~75%) than Form (46%).

**Evidence:**
```bash
$ grep -rh "function [a-zA-Z_]*\(.*\): " src/ | wc -l
132
$ grep -rh "function [a-zA-Z_]*(" src/ | wc -l
257

# Per-file sampling
# Field.php: 11/15 (73%)
# Validator.php: 9/12 (75%)
# Form.php: 6/13 (46%)
```

**Decision/Recommendation:**
1. Prioritize adding return types to Form.php (lowest coverage, highest impact)
2. Add return types to all remaining methods where PHP 7.4 allows
3. Methods returning `$this` need `: self` or `@return static`

**Follow-up Questions:**
- Which methods lack return types due to PHP 7.4 limitations (union types needed)?

---

### Q1.5 - PHPDoc Coverage

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (sample files)
- [x] Command execution

**Findings:**
**Total PHPDoc annotations across src/:**
| Annotation | Count |
|-----------|-------|
| @param | 139 |
| @return | 56 |
| @var | 67 |

**Sampled files:**
| File | @param | @return | Notes |
|------|--------|---------|-------|
| Field.php | 14 | 1 | Heavy params, light returns (native types used) |
| Form.php | 10 | 7 | Balanced coverage |

The pattern: many methods have native return types (51%) but use @param for complex types.

**Evidence:**
```bash
$ grep -rh "@param" src/ | wc -l
139
$ grep -rh "@return" src/ | wc -l
56
$ grep -rh "@var" src/ | wc -l
67
```

**Decision/Recommendation:**
1. @return annotations are sparse (56) because native types are preferred
2. @param usage is good (139) for documenting complex/union types
3. Add @return annotations where native types aren't possible (union types in PHP 7.4)

**Follow-up Questions:**
- Are there methods with neither native types nor @return annotations?

---

### Q1.6 - Template/Generic Usage Patterns

**Status:** Answered

**Investigation Method:**
- [x] Code inspection
- [x] Command execution (grep for @template)

**Findings:**
**Comprehensive generics are already in use:**

| Annotation | Count | Purpose |
|-----------|-------|---------|
| @template | 27 | Generic type parameters (TData, TOptions, etc.) |
| @template-extends | 12 | Specifying parent generic types in subclasses |
| @phpstan-type | 17 | Custom type aliases for options arrays |
| @phpstan-import-type | 23 | Importing type aliases between files |
| @phpstan-param | 8 | PHPStan-specific parameter types |

**Key patterns observed:**
1. **Field<TData>** - Fields are generic over their data type
2. **Validator<TOptions>** - Validators generic over their options array shape
3. **Widget<TData>** - Widgets generic over field data type
4. **Options arrays** - Custom types like `FieldOptionsArray`, `SelectFieldOptionsArray`

**Evidence:**
```php
// Field.php
@template TData

// Validator.php
@template TOptions of array<string, mixed>

// Subclass pattern (e.g., AnyOf.php)
@template-extends Validator<AnyOfOptionsArray>

// Type import pattern
@phpstan-import-type FieldOptionsArray from FieldOptions
@phpstan-type BooleanFieldOptionsArray array{false_values?: array<int, string>} & FieldOptionsArray
```

**Decision/Recommendation:**
Generics foundation is solid. Focus on:
1. Ensuring all subclasses properly extend with specific types (level 6 errors)
2. Add `@template-covariant` where needed for proper variance
3. Document the pattern for future contributors

**Follow-up Questions:**
- Are covariance issues causing the level 5 errors?

---

### Q1.7 - PHP 7.4 Type Constraints

**Status:** Answered

**Investigation Method:**
- [x] Documentation review
- [x] Code inspection

**Findings:**
**PHP version:** 7.4.33

**Available in PHP 7.4:**
| Feature | Syntax | Notes |
|---------|--------|-------|
| Typed properties | `public string $name;` | Yes, since 7.4 |
| Return types | `function foo(): string` | Yes |
| Nullable types | `?string`, `function(): ?int` | Yes |
| void return | `function(): void` | Yes |
| array type | `function(array $a): array` | Yes |
| object type | `function(): object` | Yes |
| iterable type | `function(): iterable` | Yes |
| self/parent | `function(): self` | Yes |
| Class types | `function(Form $f): Field` | Yes |

**NOT available until PHP 8.0+:**
| Feature | Syntax | Workaround |
|---------|--------|------------|
| Union types | `string|int` | Use PHPDoc `@param string|int` |
| Mixed type | `mixed` | Use PHPDoc `@param mixed` |
| Named arguments | `foo(name: 'value')` | Use options arrays |
| Attributes | `#[Attribute]` | Use PHPDoc annotations |
| Match expression | `match($x) { }` | Use switch or ternary |
| Constructor promotion | `__construct(public $x)` | Manual property declaration |
| Nullsafe operator | `$obj?->method()` | Use `$obj !== null ? $obj->method() : null` |
| Static return type | `function(): static` | Use `@return static` in PHPDoc |

**Evidence:**
```bash
$ php -v
PHP 7.4.33 (cli)

# composer.json requirement:
"require": {
    "php": ">=7.4"
}
```

**Decision/Recommendation:**
1. Use PHPDoc for union types (`@param string|int $value`)
2. Use PHPDoc for `mixed` type declarations
3. Use `@return static` for fluent interfaces
4. Generics via PHPDoc only (`@template`, `@phpstan-type`)

**NOTE:** User indicated willingness to break Python-esque dynamic API for better typing. This opens options like:
- Replacing dynamic `__get`/`__set` with explicit typed accessors
- Using strict constructor signatures instead of options arrays

**Follow-up Questions:**
- Should we break backward compatibility for type safety?

---

### Q1.8 - Suppressions and Weak Types

**Status:** Answered

**Investigation Method:**
- [x] Command execution (grep for mixed, @var, noinspection)

**Findings:**
**Suppressions found:**

| Type | Count | Location |
|------|-------|----------|
| `@phpstan-ignore-next-line` | 1 | SessionCSRF.php:146 |
| `@noinspection PhpUnusedParameterInspection` | 2 | CSRF.php, DefaultMeta.php |
| `@noinspection PhpPropertyOnlyWrittenInspection` | 1 | Validator.php |

**Mixed type usage:** 32 occurrences (excluding `array<string, mixed>`)

Key locations with mixed:
- `FieldList.php` - 12+ uses for generic field collections
- `Field.php` - raw_data, arguments, default values
- `FormField.php` - dynamic field access

**Evidence:**
```php
// SessionCSRF.php - only @phpstan-ignore
// @phpstan-ignore-next-line

// FieldList.php - many mixed types for generic collections
@var Field<mixed>
@return array<int, mixed>|mixed

// Field.php - raw data handling
@var array<int, mixed>|null
```

**Decision/Recommendation:**
1. **Low suppression count** - only 4 total, manageable
2. **Mixed in FieldList** - unavoidable for heterogeneous collections, but could add generic constraints
3. **Consider replacing** `Field<mixed>` with proper bounded generics when possible
4. Remove unused parameter suppressions by adding proper type narrowing

**Follow-up Questions:**
- Can we use bounded generics to eliminate some `mixed` usage?

---

### Q1.9 - Dev Dependencies

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (`composer.json`)

**Findings:**
**Current dev dependencies:**

| Package | Version | Purpose |
|---------|---------|---------|
| phpunit/phpunit | ^9.0 | Testing framework |
| rector/rector | ^2.3 | Automated refactoring |
| phpstan/phpstan | ^2.1 | Static analysis (primary) |
| friendsofphp/php-cs-fixer | ^3.93 | Code style |

**Runtime dependencies affecting types:**
| Package | Version | Type implications |
|---------|---------|------------------|
| nesbot/carbon | ^2.0 \|\| ^3.0 | DateTime handling, needs Carbon types |
| gettext/gettext | ^5.7 | i18n functions |
| ext-openssl | * | Crypto functions |

**Evidence:**
```json
"require-dev": {
    "phpunit/phpunit": "^9.0",
    "rector/rector": "^2.3",
    "phpstan/phpstan": "^2.1",
    "friendsofphp/php-cs-fixer": "^3.93"
}
```

**Decision/Recommendation:**
**Missing packages to add:**
1. `vimeo/psalm` - for Psalm analysis + taint analysis
2. `phpstan/phpstan-strict-rules` - stricter PHPStan checks
3. `phpstan/phpstan-phpunit` - PHPUnit-aware analysis
4. `phpstan/phpstan-deprecation-rules` - deprecation warnings
5. `nesbot/carbon-phpstan` - Carbon type stubs (if exists)

**Follow-up Questions:**
- Is there a Carbon PHPStan extension?

---

### Q1.10 - Custom PHPStan Extensions

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (phpstan.neon, phpstan/)

**Findings:**
**Custom extension exists:** `ValidatorThrowTypeExtension`

**Location:** `phpstan/Extensions/ValidatorThrowTypeExtension.php`

**Purpose:** Tells PHPStan that validator `__invoke()` calls don't propagate exceptions to callers. This is because `StopValidation` and `ValidationError` are caught internally by `Field::runValidationChain()`.

**Supporting metadata:** `phpstan/Metadata/ValidatorMetadata.php`
- Maps validators to exception types (StopValidation vs ValidationError)
- Defines type narrowing rules (e.g., `DataRequired` removes `null` from union)

**Evidence:**
```php
// ValidatorThrowTypeExtension.php
class ValidatorThrowTypeExtension implements DynamicMethodThrowTypeExtension
{
    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return '__invoke' === $methodReflection->getName()
            && $this->isValidatorClass(...);
    }

    public function getThrowTypeFromMethodCall(...): ?Type
    {
        return null; // No exceptions propagate to caller
    }
}

// ValidatorMetadata.php
public const THROW_TYPES = [
    DataRequired::class  => StopValidation::class,
    Length::class        => ValidationError::class,
    // ...
];

public const TYPE_NARROWING = [
    DataRequired::class  => ['removes' => ['null']],
    Email::class         => ['removes' => ['null'], 'narrows_to' => 'non-empty-string'],
    // ...
];
```

**Decision/Recommendation:**
1. **Type narrowing metadata is prepared** but not yet implemented as a PHPStan rule
2. Consider implementing a `TypeNarrowingExtension` using the metadata
3. Excellent foundation for advanced static analysis

**Follow-up Questions:**
- Should we implement the type narrowing extension to use the metadata?

---

## Section 2: PHPStan Configuration & Baseline

### Q2.1 - PHPStan Level Differences

**Status:** Needs Investigation

**Investigation Method:**
- [ ] Documentation review (PHPStan docs)

**Findings:**
[TODO: Document what each level adds]

**Evidence:**
```
[TODO]
```

**Decision/Recommendation:**
[TODO]

**Follow-up Questions:**
- [TODO]

---

### Q2.2 - Strict Rules Extension

**Status:** Needs Investigation

**Investigation Method:**
- [ ] Documentation review
- [ ] Web search

**Findings:**
[TODO]

**Evidence:**
```
[TODO]
```

**Decision/Recommendation:**
[TODO]

**Follow-up Questions:**
- [TODO]

---

### Q2.3 - Deprecation Rules Extension

**Status:** Needs Investigation

**Investigation Method:**
- [ ] Documentation review

**Findings:**
[TODO]

**Evidence:**
```
[TODO]
```

**Decision/Recommendation:**
[TODO]

**Follow-up Questions:**
- [TODO]

---

### Q2.4 - Bleeding Edge Features

**Status:** Needs Investigation

**Investigation Method:**
- [ ] Documentation review
- [ ] Command execution

**Findings:**
[TODO]

**Evidence:**
```
[TODO]
```

**Decision/Recommendation:**
[TODO]

**Follow-up Questions:**
- [TODO]

---

### Q2.5 - Relevant Extensions

**Status:** Needs Investigation

**Investigation Method:**
- [ ] Web search (packagist)
- [ ] Documentation review

**Findings:**
[TODO: Carbon extension, PHPUnit extension, etc.]

**Evidence:**
```
[TODO]
```

**Decision/Recommendation:**
[TODO]

**Follow-up Questions:**
- [TODO]

---

### Q2.6 - Magic Method Handling

**Status:** Needs Investigation

**Investigation Method:**
- [ ] Code inspection (__get, __set usage)
- [ ] Documentation review

**Findings:**
[TODO]

**Evidence:**
```
[TODO]
```

**Decision/Recommendation:**
[TODO]

**Follow-up Questions:**
- [TODO]

---

### Q2.7 - Acceptable Ignores

**Status:** Needs Investigation

**Investigation Method:**
- [ ] Documentation review
- [ ] Code inspection

**Findings:**
[TODO]

**Evidence:**
```
[TODO]
```

**Decision/Recommendation:**
[TODO]

**Follow-up Questions:**
- [TODO]

---

### Q2.8 - ArrayAccess Configuration

**Status:** Needs Investigation

**Investigation Method:**
- [ ] Code inspection (Form.php)
- [ ] Documentation review

**Findings:**
[TODO]

**Evidence:**
```
[TODO]
```

**Decision/Recommendation:**
[TODO]

**Follow-up Questions:**
- [TODO]

---

### Q2.9 - Iterable/Generic Strictness

**Status:** Needs Investigation

**Investigation Method:**
- [ ] Documentation review

**Findings:**
[TODO]

**Evidence:**
```
[TODO]
```

**Decision/Recommendation:**
[TODO]

**Follow-up Questions:**
- [TODO]

---

### Q2.10 - CI Integration

**Status:** Needs Investigation

**Investigation Method:**
- [ ] Code inspection (.github/workflows/)
- [ ] Documentation review

**Findings:**
[TODO]

**Evidence:**
```
[TODO]
```

**Decision/Recommendation:**
[TODO]

**Follow-up Questions:**
- [TODO]

---

## Section 3: Psalm Setup & Configuration

[Template repeats for Q3.1 through Q3.10...]

---

## Section 4: Taint Analysis

[Template repeats for Q4.1 through Q4.10...]

---

## Section 5: Field Classes Typing

[Template repeats for Q5.1 through Q5.10...]

---

## Section 6: Validator Classes Typing

[Template repeats for Q6.1 through Q6.10...]

---

## Section 7: Widget Classes Typing

[Template repeats for Q7.1 through Q7.10...]

---

## Section 8: Form & Meta Typing

[Template repeats for Q8.1 through Q8.10...]

---

## Section 9: CSRF & Security Typing

[Template repeats for Q9.1 through Q9.10...]

---

## Section 10: Generics & Templates Strategy

[Template repeats for Q10.1 through Q10.10...]

---

## Section 11: Internal Utilities Typing

[Template repeats for Q11.1 through Q11.10...]

---

## Section 12: Testing Type Coverage

[Template repeats for Q12.1 through Q12.10...]

---

## Investigation Summary

### Completed Sections
- [ ] Section 1: Current State Assessment
- [ ] Section 2: PHPStan Configuration & Baseline
- [ ] Section 3: Psalm Setup & Configuration
- [ ] Section 4: Taint Analysis
- [ ] Section 5: Field Classes Typing
- [ ] Section 6: Validator Classes Typing
- [ ] Section 7: Widget Classes Typing
- [ ] Section 8: Form & Meta Typing
- [ ] Section 9: CSRF & Security Typing
- [ ] Section 10: Generics & Templates Strategy
- [ ] Section 11: Internal Utilities Typing
- [ ] Section 12: Testing Type Coverage

### Blocking Issues
[Document any issues that prevent progress]

1. [Issue description] - [Proposed resolution]

### Key Decisions Made
[Summary of major typing decisions]

1. [Decision]: [Rationale]

### Implementation Priority Order
[After investigation, list the recommended implementation order]

1. [First priority]
2. [Second priority]
...

### New Questions Discovered
[Questions that arose during investigation that need answering]

1. [Question]
2. [Question]
...

### Tool Versions Used
- PHPStan: [version]
- Psalm: [version]
- PHP: 7.4.x
- PHPUnit: [version]

---

## Completion Checklist

Before outputting the completion promise, verify:

- [ ] All 120 questions have been investigated (12 sections x 10 questions)
- [ ] Each answer includes evidence (command output, code, or documentation links)
- [ ] All blocking issues are documented with proposed solutions
- [ ] Key decisions are summarized
- [ ] Implementation priority is established
- [ ] New questions are documented for next iteration (if any)

When all above criteria are met, output:

<promise>INVESTIGATION_COMPLETE</promise>
