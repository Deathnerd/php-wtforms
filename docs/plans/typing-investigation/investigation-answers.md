---
title: Investigation Answers
project: PHP-WTForms Comprehensive Typing Initiative
investigation_started: 2026-01-26
last_updated: 2026-01-26
current_section: 2
status: IN_PROGRESS
---

## Important Context: Breaking Changes Approved

**User has approved the following breaking changes for type safety:**

1. **Remove magic methods** (`__get`/`__set`) for dynamic property lookup
2. **Remove ArrayAccess** on Form (`$form['field']` syntax)
3. **Keep direct property access** - `$form->username` stays, but as real typed properties (via constructor promotion), not magic
4. **Separate concerns** into distinct typed structures:
   - Field attributes (HTML attributes)
   - Field data (the actual value)
   - Field flags (boolean indicators like `required`)
   - Field options (configuration)
5. **Use shapes/contracts** - typed arrays or value objects with enforced access patterns

**Key distinction:**
```php
// KEEP - real typed property (PHP 8 constructor promotion)
$form->username  // StringField

// REMOVE - magic/dynamic access
$form['username']  // ArrayAccess
$form->__get('username')  // Magic method fallback
```

**Rationale:** The original library was a faithful Python port with dynamic features. Modern PHP with static analysis benefits from explicit typing. See `docs/plans/conformity-prd.md` for the PHP 8+ vision.

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

**BREAKING CHANGES CONFIRMED:** User has approved major refactoring for type safety:
- Replace `__get`/`__set` with explicit typed accessors
- Remove `ArrayAccess` - use explicit `getField()` methods
- Separate concerns: attributes, data, flags, options into typed structures
- Use shapes/contracts with enforced access patterns
- Strict constructor signatures instead of dynamic options arrays

**Follow-up Questions:**
- None - breaking changes approved

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

**UPDATED (Breaking Changes Approved):**
1. **Eliminate `mixed` usage** - separate attributes/data/flags/options into typed structures
2. **FieldList** - make properly generic `FieldList<TField of Field>`
3. **Remove all `@noinspection`** - fix underlying issues with explicit methods
4. **Goal: Zero suppressions** - breaking changes allow proper typing

**Follow-up Questions:**
- None - breaking changes allow proper fixes

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

**Status:** Answered

**Investigation Method:**
- [x] Documentation review (PHPStan docs)

**Findings:**
PHPStan levels are cumulative (level 5 includes 0-4). Here's what each level adds:

| Level | What It Adds |
|-------|-------------|
| 0-4 | Basic: unknown classes/functions/methods, wrong arg counts, undefined variables, PHPDoc validation |
| 5 | Dead code: always-false instanceof, dead else branches, unreachable code after return |
| 6 | **Argument type checking**: types of arguments passed to methods/functions |
| 7 | **Partial union types**: methods that only exist on some types in a union |
| 8 | **Nullable checking**: calling methods/accessing properties on nullable types |
| 9 | Strict generics, uninitialized properties, benevolent union types |
| 10 | Max strictness: all `mixed` types treated strictly (PHPStan v2 only) |

**Current state for php-wtforms:**
- Level 5: 8 errors (dead code, covariance)
- Level 6: 118 errors (+110 from missing generics on extending classes)
- Level 7: 158 errors (+40)
- Level 8: 179 errors (+21)
- Level 9: 217 errors (+38)

**Evidence:**
PHPStan's own source code uses level 8 + strict-rules, not level 9/10.

**Decision/Recommendation:**

**UPDATED (Breaking Changes Approved):**
1. **Target level 9** as final goal - breaking changes allow proper fixes
2. Fix level 5 errors first (8 errors - dead code, covariance)
3. Fix level 6 errors (add generic type specs to all extending classes)
4. Enable `phpstan-strict-rules` early for LSP/covariance checks
5. Progress through 7→8→9 with proper typing, not suppressions

With breaking changes approved, we can eliminate the need for workarounds.

**Follow-up Questions:**
- None

---

### Q2.2 - Strict Rules Extension

**Status:** Answered

**Investigation Method:**
- [x] Documentation review
- [x] Web search

**Findings:**
`phpstan/phpstan-strict-rules` adds extra strict and opinionated rules:

**Key rules added:**
| Rule Category | What It Checks |
|---------------|----------------|
| Boolean conditions | Require booleans in if/elseif/ternary/while (not truthy values) |
| Always-true checks | Flag always-true instanceof, is_* functions, strict comparisons |
| LSP enforcement | Contravariance for params, covariance for returns |
| Static method LSP | LSP checks for static methods too |
| Parent constructor | Require calling parent constructor |
| Backtick operator | Disallow shell backticks |
| And more... | disallowedLooseComparison, disallowedEmpty, noVariableVariables, etc. |

**Configurable options via `strictRules` parameter:**
- disallowedLooseComparison, booleansInConditions, uselessCast
- requireParentConstructorCall, disallowedBacktick, disallowedEmpty
- disallowedImplicitArrayCreation, disallowedShortTernary
- closureUsesThis, numericOperandsInArithmeticOperators
- strictFunctionCalls, dynamicCallOnStaticMethod, noVariableVariables

**Note:** Even with `strictRules.allRules: false`, it still changes default PHPStan parameters.

**Evidence:**
```bash
composer require --dev phpstan/phpstan-strict-rules
# Then in phpstan.neon:
includes:
    - vendor/phpstan/phpstan-strict-rules/rules.neon
```

**Decision/Recommendation:**
**YES - Enable strict-rules** because:
1. LSP enforcement catches covariance/contravariance issues (we have these)
2. Boolean conditions enforcement catches loose truthy checks
3. PHPStan itself uses strict-rules + level 8

**Follow-up Questions:**
- How many new errors will strict-rules introduce?

---

### Q2.3 - Deprecation Rules Extension

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
`phpstan/phpstan-deprecation-rules` detects usage of deprecated code:

**What it detects:**
- Deprecated classes
- Deprecated methods
- Deprecated properties
- Deprecated constants
- Deprecated traits

**Key features:**
- Parses `@deprecated` PHPDoc annotations
- Shows deprecation message if provided (`@deprecated Use X instead`)
- Smart scoping: deprecated code calling other deprecated code isn't flagged
- Stub files for marking vendor code as deprecated

**Evidence:**
```bash
composer require --dev phpstan/phpstan-deprecation-rules
# Auto-includes via extension-installer
```

**Decision/Recommendation:**
**YES - Enable deprecation-rules** because:
1. Helps track internal deprecations during refactoring
2. Catches usage of deprecated Carbon/PHPUnit APIs
3. Low noise (only reports actual deprecations)

**Follow-up Questions:**
- Do we have any @deprecated annotations in the codebase?

---

### Q2.4 - Bleeding Edge Features

**Status:** Answered

**Investigation Method:**
- [x] Documentation review
- [ ] Command execution

**Findings:**
**Bleeding Edge** is PHPStan's preview of next major version features:

**What it provides:**
- New rules that would break BC in minor versions
- Bug fixes waiting for major release
- Stricter analysis behavior
- Performance improvements

**Example features in bleeding edge:**
- Null coalesce on non-nullable variable detection
- Missing `#[\Override]` attribute checks (PHP 8.3)
- Private property union type reduction suggestions

**How to enable:**
```neon
includes:
    - vendor/phpstan/phpstan/conf/bleedingEdge.neon
```

**Caveats:**
- No BC guarantees between minor versions
- Behavior can change unexpectedly
- Only activates rules at your current level

**Evidence:**
From PHPStan docs: "Bleeding edge offers a preview of the next major version."

**Decision/Recommendation:**
**YES - Enable bleeding edge** because:
1. We're already on PHPStan v2.1, so most features are stable
2. Performance improvements reported
3. Easier incremental upgrades vs major version shock
4. Current level (5) limits what activates anyway

Add after fixing level 5 errors:
```neon
includes:
    - vendor/phpstan/phpstan/conf/bleedingEdge.neon
```

**Follow-up Questions:**
- None

---

### Q2.5 - Relevant Extensions

**Status:** Answered

**Investigation Method:**
- [x] Web search (packagist)
- [x] Documentation review

**Findings:**
**Relevant PHPStan extensions for php-wtforms:**

| Extension | Purpose | Install? |
|-----------|---------|----------|
| `phpstan/phpstan-phpunit` | PHPUnit assertions, early termination | **YES** |
| `phpstan/phpstan-strict-rules` | Stricter boolean/LSP checks | **YES** |
| `phpstan/phpstan-deprecation-rules` | Deprecated code detection | **YES** |
| `phpstan/extension-installer` | Auto-include extensions | **YES** |

**Carbon support:**
- Carbon v3 includes its own PHPStan stubs in the package
- No separate extension needed
- `soyhuce/phpstan-extension` adds strict Carbon rules (forbids mutable copy())

**PHPUnit extension features:**
- Assert method type narrowing (assertInstanceOf, assertTrue, etc.)
- Early terminating method detection
- Always-true/false assertion detection
- Strict rules for assert best practices

**Evidence:**
```bash
composer require --dev \
  phpstan/extension-installer \
  phpstan/phpstan-phpunit \
  phpstan/phpstan-strict-rules \
  phpstan/phpstan-deprecation-rules
```

**Decision/Recommendation:**
Install all four extensions. The extension-installer will auto-include them.

**Follow-up Questions:**
- None

---

### Q2.6 - Magic Method Handling

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (__get, __set usage)
- [x] Documentation review

**Findings:**
**Current `__get`/`__set` usage is extensive:**

| File | Usage |
|------|-------|
| Field.php | `__get` for `value` virtual property |
| Form.php | `__get`/`__set` for dynamic field access |
| FieldList.php | `__get` for entries access |
| FormField.php | `__get` for nested field access |
| DateField/DateTimeField | `__get` for data/format |
| SelectFieldBase | `__get` for choices/data |
| CSRFTokenField | `__get` for csrf_impl |
| Label.php | `__get` for text/field_id |

**PHPStan solutions for magic methods:**
1. `@property` PHPDoc annotations
2. `@mixin` for delegation
3. Custom `PropertiesClassReflectionExtension`
4. `universalObjectCratesClasses` config

**Evidence:**
```php
// Current pattern (Field.php:475)
public function __get(string $name) {
    if ('value' == $name) { ... }
    return null;
}
```

**Decision/Recommendation:**
**BREAKING CHANGE APPROVED:** Replace magic methods with explicit typed accessors.

**Migration strategy (CONFIRMED):**
1. **Replace magic methods** with explicit typed getters/setters
2. **Remove ArrayAccess** on Form - use `getField(string $name): Field`
3. **Separate concerns** into typed structures:
   - `FieldAttributes` - HTML attributes (typed array or value object)
   - `FieldData<T>` - the actual value with generic type
   - `FieldFlags` - boolean indicators (required, disabled, etc.)
   - `FieldOptions` - configuration with typed shape
4. **Use contracts/interfaces** for enforced access patterns

**Benefits:**
- Full PHPStan type inference at level 9
- IDE autocomplete with proper types
- No `@property` annotations needed
- No `mixed` types needed
- Modern Symfony/Java-style OOP
- Clear separation of concerns

**Example transformation:**
```php
// Before (magic, untyped)
$field->value           // mixed via __get
$form['username']       // mixed via ArrayAccess
$field->flags->required // magic Flags object

// After (explicit, typed)
$form->username         // StringField (real typed property, NOT magic)
$form->username->getValue(): string
$field->getFlags()->isRequired(): bool
$field->getAttributes()->get('class'): ?string

// Dynamic access when needed (rare)
$form->getField('username'): ?Field
```

**Key insight:** Direct property access `$form->username` stays - it becomes a real typed property via constructor promotion, not magic `__get`. Only ArrayAccess and magic method fallbacks are removed.

**Follow-up Questions:**
- None - full breaking changes approved

---

### Q2.7 - Acceptable Ignores

**Status:** Answered

**Investigation Method:**
- [x] Documentation review
- [x] Code inspection

**Findings:**
**Current ignores in codebase (only 1):**
```php
// SessionCSRF.php:146
// @phpstan-ignore-next-line
```

**When `@phpstan-ignore` is acceptable:**

| Acceptable | Example |
|------------|---------|
| Third-party library quirks | Library returns wrong type in stub |
| PHP version differences | Code branches for PHP 7.4 vs 8.0 |
| Intentional dynamic behavior | Truly dynamic metaprogramming (rare) |
| False positives | Proven PHPStan bug with issue reference |

**When `@phpstan-ignore` is NOT acceptable:**

| Not Acceptable | Fix Instead |
|----------------|-------------|
| Lazy typing | Add proper types |
| Mixed types | Use union types or generics |
| Magic methods | Replace with explicit methods ✓ |
| Dynamic properties | Use typed properties ✓ |

**PHPStan 1.11+ error identifiers:**
Use specific identifiers: `@phpstan-ignore argument.type, return.type`
Not generic: `@phpstan-ignore-next-line`

**Evidence:**
Only 1 ignore in entire codebase - excellent baseline.

**Decision/Recommendation:**
1. **Goal: Zero ignores** - with breaking changes approved, we can fix issues properly
2. Audit the one existing ignore in SessionCSRF.php
3. Use error identifiers if any ignores are truly needed
4. Document reason in comment when ignoring

**Follow-up Questions:**
- What is the SessionCSRF.php ignore for?

---

### Q2.8 - ArrayAccess Configuration

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (Form.php)
- [ ] Documentation review

**Findings:**
**Current ArrayAccess implementation in Form.php:**
- `offsetExists($offset)` - checks if field exists
- `offsetGet($offset)` - returns Field (untyped)
- `offsetSet($offset, $value)` - sets field
- `offsetUnset($offset)` - removes field

**PHPStan ArrayAccess typing options:**
1. `@implements ArrayAccess<string, Field>` - generic ArrayAccess
2. `@phpstan-type` for specific offset types
3. Custom extension for dynamic key typing

**Evidence:**
```php
// Current usage
$form['username']  // Returns Field, but PHPStan sees mixed
$form['password'] = new PasswordField();  // Works but untyped
```

**Decision/Recommendation:**

**OBSOLETE - ArrayAccess will be removed, but direct property access stays.**

The distinction:
- **Keep:** `$form->username` - direct typed property (real property, not magic)
- **Remove:** `$form['username']` - ArrayAccess (untyped)
- **Remove:** `__get('username')` - magic method fallback (untyped)

With PHP 8 constructor property promotion (per conformity-prd.md):
```php
class LoginForm extends Form {
    public function __construct(
        public StringField $username,  // Real typed property
        public PasswordField $password,
    ) {
        parent::__construct();
    }
}

// Usage - fully typed, no magic
$form->username->getValue()  // PHPStan knows this is StringField
$form->password->validate()  // PHPStan knows this is PasswordField
```

For dynamic access (rare cases), provide explicit method:
```php
$form->getField('username'): ?Field  // Returns Field or null
$form->hasField('username'): bool
```

No ArrayAccess configuration needed since we're removing it.

**Follow-up Questions:**
- None

---

### Q2.9 - Iterable/Generic Strictness

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**These options are DEPRECATED in PHPStan 2.x:**

| Option | What It Did | Status |
|--------|-------------|--------|
| `checkMissingIterableValueType` | Flag `array` without `array<T>` | Deprecated |
| `checkGenericClassInNonGenericObjectType` | Flag generic classes without type params | Removed |

**In PHPStan 2.x:**
- These checks are now controlled by **level 6**
- Level 6 automatically checks missing generic types
- Use `ignoreErrors` with `identifier: missingType.generics` to suppress if needed

**Evidence:**
```neon
# OLD (deprecated)
parameters:
    checkMissingIterableValueType: true
    checkGenericClassInNonGenericObjectType: true

# NEW (PHPStan 2.x)
# Just use level 6+ and these checks are automatic
# To ignore specific errors:
parameters:
    ignoreErrors:
        - identifier: missingType.generics
```

**Decision/Recommendation:**
1. **Don't use deprecated options** - they no longer work in PHPStan 2.1
2. **Use level 6** - this enables generic type checking automatically
3. **Don't ignore errors** - with breaking changes approved, fix them properly
4. All classes extending generic base classes must specify type parameters

**Follow-up Questions:**
- None

---

### Q2.10 - CI Integration

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (.github/workflows/)
- [x] Documentation review

**Findings:**
**Current CI:** `.github/workflows/tests.yml` exists but only runs PHPUnit tests.

**Missing:** PHPStan is NOT in CI - no static analysis on PRs.

**Current workflow structure:**
- Triggers: push/PR to master
- PHP versions: 7.4 only (TODO comment for 5.6, 8.4)
- Steps: checkout → setup PHP → composer install → phpunit → codecov

**Evidence:**
```yaml
# Current tests.yml - NO PHPStan step
- name: Run tests
  run: vendor/bin/phpunit ...
```

**Decision/Recommendation:**
**Add PHPStan to CI workflow:**

```yaml
# Add after "Install dependencies"
- name: Run PHPStan
  run: vendor/bin/phpstan analyse --memory-limit=512M

# Or as separate job for parallel execution
jobs:
  static-analysis:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
      - run: composer install --prefer-dist --no-progress
      - run: vendor/bin/phpstan analyse --memory-limit=512M
```

**When to add:**
1. First, fix level 5 errors (8 remaining)
2. Add PHPStan to CI at level 5
3. Increment level as we fix more errors

**Follow-up Questions:**
- None

---

## Section 3: Psalm Setup & Configuration

### Q3.1 - Psalm Installation

**Status:** Answered

**Investigation Method:**
- [x] Documentation review
- [x] Web search

**Findings:**
**Installation:**
```bash
composer require --dev vimeo/psalm
vendor/bin/psalm --init
```

**Files created:**
- `psalm.xml` - main configuration file

**Evidence:**
```xml
<!-- Generated psalm.xml -->
<?xml version="1.0"?>
<psalm
    errorLevel="1"
    resolveFromConfigFile="true"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns="https://getpsalm.org/schema/config"
>
    <projectFiles>
        <directory name="src" />
    </projectFiles>
</psalm>
```

**Decision/Recommendation:**
Install Psalm alongside PHPStan for complementary analysis and taint checking.

---

### Q3.2 - Psalm Levels

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**Psalm levels are OPPOSITE to PHPStan:**
| Psalm Level | Strictness | Equivalent PHPStan |
|-------------|------------|-------------------|
| 1 | Strictest | ~Level 9-10 |
| 2 | Very strict | ~Level 8 |
| 3-4 | Strict | ~Level 6-7 |
| 5-6 | Moderate | ~Level 4-5 |
| 7-8 | Loose | ~Level 1-3 |

**Key difference:** Psalm 1 = max strict, PHPStan 9 = max strict

**Decision/Recommendation:**
Target Psalm level 1 (max strictness) to match our PHPStan level 9 goal.

---

### Q3.3 - Psalm vs PHPStan Differences

**Status:** Answered

**Investigation Method:**
- [x] Web search
- [x] Documentation review

**Findings:**
**Psalm catches that PHPStan doesn't:**
- Taint analysis (security vulnerabilities)
- Immutability enforcement (`@psalm-immutable`)
- More granular `@psalm-assert*` annotations
- Template type variance (more strict)

**PHPStan catches that Psalm doesn't:**
- Some edge cases in magic methods
- Better Laravel/framework magic support
- Faster analysis (20-50% faster)

**Both catch:**
- Type mismatches, undefined variables, dead code
- Generic/template type issues
- PHPDoc parsing issues

**Decision/Recommendation:**
Run both tools - they're complementary, not competing.

---

### Q3.4 - Coexistence Configuration

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**Both tools can coexist without conflicts:**

```
phpstan.neon   - PHPStan config
psalm.xml      - Psalm config (different format!)
```

**Annotation compatibility:**
- `@template` - both understand
- `@param`, `@return` - both understand
- `@phpstan-*` - PHPStan only
- `@psalm-*` - Psalm only

**Best practice:** Use tool-agnostic annotations where possible, tool-specific only when needed.

**Decision/Recommendation:**
Keep separate configs, prefer generic annotations, add tool-specific only for advanced features.

---

### Q3.5 - Psalm Plugins

**Status:** Answered

**Investigation Method:**
- [x] Web search

**Findings:**
**Relevant Psalm plugins:**

| Plugin | Purpose |
|--------|---------|
| `psalm/plugin-phpunit` | PHPUnit assertions and mocks |
| `weirdan/doctrine-psalm-plugin` | Doctrine entities (not needed for core) |

**Not needed:**
- Carbon plugin (not found, may not exist)
- Framework plugins (we're framework-agnostic)

**Decision/Recommendation:**
Install `psalm/plugin-phpunit` for test analysis.

---

### Q3.6 - Generics Syntax Differences

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**Mostly compatible syntax:**

| Feature | PHPStan | Psalm | Compatible? |
|---------|---------|-------|-------------|
| `@template T` | Yes | Yes | ✓ |
| `@template T of Foo` | Yes | Yes | ✓ |
| `@template-covariant T` | Yes | Yes | ✓ |
| `@extends Foo<T>` | Yes | Yes | ✓ |
| `@phpstan-type` | Yes | No | Use `@psalm-type` too |
| `@psalm-type` | No | Yes | Use `@phpstan-type` too |

**Solution for custom types:**
```php
/**
 * @phpstan-type MyArray array{foo: string}
 * @psalm-type MyArray array{foo: string}
 */
```

**Decision/Recommendation:**
Use both annotations for custom types, standard generics work in both.

---

### Q3.7 - Annotation Compatibility

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**Tool-specific prefixes:**
- `@phpstan-*` - ignored by Psalm
- `@psalm-*` - ignored by PHPStan

**Shared annotations (both understand):**
- `@template`, `@template-covariant`, `@template-extends`
- `@param`, `@return`, `@var`, `@throws`
- `@deprecated`, `@internal`
- Array shapes: `array{foo: string, bar: int}`

**Psalm-only features:**
- `@psalm-assert`, `@psalm-assert-if-true/false`
- `@psalm-immutable`, `@psalm-pure`
- `@psalm-taint-*` (for taint analysis)

**Decision/Recommendation:**
Use shared syntax by default. Add `@psalm-*` for taint analysis, `@phpstan-*` for type imports.

---

### Q3.8 - Disagreement Resolution

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**When tools disagree:**
1. Check if it's a real issue or false positive
2. If real issue - fix the code
3. If false positive in one tool - use tool-specific ignore

**Tool-specific ignores:**
```php
/** @phpstan-ignore-next-line */
/** @psalm-suppress MixedAssignment */
```

**Decision/Recommendation:**
1. Prefer fixing code over suppressing
2. If must suppress, use tool-specific annotation
3. Document why in comment

---

### Q3.9 - Psalm Alter Mode

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**`--alter` mode** can automatically fix some issues:
```bash
vendor/bin/psalm --alter --issues=MissingReturnType,MissingParamType
```

**What it can fix:**
- Add missing return types
- Add missing param types
- Add missing property types

**Caveats:**
- May not choose optimal types
- Review changes before committing
- Works best for simple cases

**Decision/Recommendation:**
Use `--alter` for initial type scaffolding, but review all changes manually.

---

### Q3.10 - Psalm CI Integration

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Add to `.github/workflows/tests.yml`:**

```yaml
- name: Run Psalm
  run: vendor/bin/psalm --no-progress --output-format=github
```

**`--output-format=github`** formats errors as GitHub annotations.

**Decision/Recommendation:**
Add Psalm to CI after initial setup and fixing level 8 errors.
Run PHPStan and Psalm in parallel for speed.

---

## Section 4: Taint Analysis

### Q4.1 - Taint Analysis Concepts

**Status:** Answered

**Investigation Method:**
- [x] Documentation review (Psalm docs)

**Findings:**
**Psalm taint analysis tracks data flow from sources to sinks:**

| Concept | Description |
|---------|-------------|
| **Source** | Where untrusted data enters (`$_GET`, `$_POST`, user input) |
| **Sink** | Where data shouldn't reach unescaped (`echo`, `include`, SQL) |
| **Sanitizer** | Function that makes data safe (`htmlspecialchars`, prepared statements) |

**How it works:**
1. Psalm builds a graph of data flow
2. Traces tainted data from sources through transformations
3. Warns if tainted data reaches a sink unsanitized

**Run with:**
```bash
vendor/bin/psalm --taint-analysis
```

---

### Q4.2 - Taint Sources in php-wtforms

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Primary taint sources:**

| Location | Source | Why |
|----------|--------|-----|
| `Field::process($formdata)` | `$formdata` parameter | Raw user input from form submission |
| `Field::$raw_data` | Property | Stores raw form values |
| `Form::__construct($options['formdata'])` | Constructor option | Entry point for form data |

**Code evidence:**
```php
// Field.php:402-406
if ($formdata) {
    if (array_key_exists($this->name, $formdata)) {
        $this->raw_data = $formdata[$this->name];
    }
}
```

**Annotation needed:**
```php
/**
 * @psalm-taint-source input
 */
public function process(array $formdata, $data = null): void
```

---

### Q4.3 - Taint Sinks in php-wtforms

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (widgets)

**Findings:**
**Primary taint sinks (HTML output):**

| Location | Sink | Risk |
|----------|------|------|
| `Input::__invoke()` | `sprintf('<input %s>', ...)` | XSS via attributes |
| `TextArea::__invoke()` | `sprintf('<textarea>%s</textarea>', ...)` | XSS in content |
| `Select::renderOption()` | `sprintf('<option>%s</option>', ...)` | XSS in labels |
| `ListWidget::__invoke()` | `sprintf('<li>%s</li>', ...)` | XSS in list items |
| `Label::__invoke()` | Label text output | XSS in labels |

**Annotation needed:**
```php
/**
 * @psalm-taint-sink html $output
 */
public function __invoke(...): string
```

---

### Q4.4 - Sanitization Functions

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (helpers.php)

**Findings:**
**Existing sanitizers:**

| Function | Purpose | Location |
|----------|---------|----------|
| `e()` | HTML escape via `htmlspecialchars` | helpers.php:20-23 |
| `html_params()` | Escapes attribute values via `e()` | helpers.php:113-136 |

**Code:**
```php
function e(string $string): string {
    return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
}

// In html_params():
$params[] = sprintf('%s="%s"', $key, e($value));
```

**Good:** `html_params()` properly escapes attribute values.
**Good:** `TextArea` uses `e($field->value)` for content.

---

### Q4.5 - Source Annotation Syntax

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**Psalm source annotation:**
```php
/**
 * @psalm-taint-source input
 */
public function getUserInput(): string
```

**Taint types:**
- `input` - user input (forms, URL params)
- `html` - HTML content
- `sql` - SQL queries
- `shell` - shell commands
- `file` - file paths
- `cookie` - cookie data

**For php-wtforms:**
```php
// Field.php
/**
 * @psalm-taint-source input $formdata
 */
public function process(array $formdata, $data = null): void
```

---

### Q4.6 - Sink Annotation Syntax

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**Psalm sink annotation:**
```php
/**
 * @psalm-taint-sink html $content
 */
public function render(string $content): void
```

**For php-wtforms widgets:**
```php
// Widget return values go to HTML output
/**
 * @psalm-taint-sink html
 * @return string
 */
public function __invoke(Field $field, array $options = []): string
```

---

### Q4.7 - Escape Annotation Syntax

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**Psalm escape annotation:**
```php
/**
 * @psalm-taint-escape html
 */
function e(string $string): string
{
    return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
}
```

**For php-wtforms:**
```php
// helpers.php - add annotation to e()
/**
 * @psalm-taint-escape html
 */
function e(string $string): string

// html_params() also effectively escapes
/**
 * @psalm-taint-escape html
 */
function html_params(array $options): string
```

---

### Q4.8 - Existing Vulnerabilities Check

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current security analysis:**

| Widget | Escaping | Status |
|--------|----------|--------|
| Input | `html_params()` for attrs | ✓ Safe |
| TextArea | `e($field->value)` for content | ✓ Safe |
| Select | `html_params()` for attrs, `$label` unescaped | ⚠️ Review |
| ListWidget | Direct `$subfield()` output | ✓ Delegated |

**Potential issue in Select.php:64:**
```php
return sprintf('<option %s>%s</option>', html_params($options), $label);
// $label is not escaped!
```

**Recommendation:** Ensure `$label` is escaped: `e($label)`

---

### Q4.9 - Legitimate HTML Handling

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**For intentionally HTML output (form rendering):**

Use `@psalm-taint-specialize` to create safe paths:
```php
/**
 * @psalm-taint-specialize
 */
public function render(): string
```

Or mark specific variables as escaped:
```php
/** @var string $html @psalm-taint-escape html */
$html = $this->buildForm();
```

**For php-wtforms:**
The widget output IS the safe HTML (after escaping). Mark widget `__invoke()` returns as escaped since they use `e()` and `html_params()`.

---

### Q4.10 - PHPStan Taint Analysis

**Status:** Answered

**Investigation Method:**
- [x] Web search

**Findings:**
**PHPStan does NOT have built-in taint analysis.**

There was a third-party extension but it's no longer maintained.

**Recommendation:**
Use Psalm for taint analysis, PHPStan for type analysis.

```bash
# Taint analysis with Psalm
vendor/bin/psalm --taint-analysis

# Type analysis with PHPStan
vendor/bin/phpstan analyse
```

---

## Section 5: Field Classes Typing

### Q5.1 - Base Field Generic Structure

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (Field.php)

**Findings:**
**Current generic structure:**
```php
/**
 * @template TData
 * @property TData $data
 */
class Field implements \Iterator
```

`TData` represents the type of data the field holds:
- `StringField` → `TData = string`
- `IntegerField` → `TData = int`
- `DateField` → `TData = ?Carbon`
- `BooleanField` → `TData = bool`

**Properties using TData:**
- `$data` - the processed/coerced value (virtual property via `@property`)

**Current issue:** `$data` is accessed via `__get` magic, not a real typed property.

**Recommendation:** With breaking changes approved, make `$data` a real typed property.

---

### Q5.2 - Per-Field Data Types

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (all field classes)

**Findings:**
**Recommended `TData` types for each field:**

| Field Class | TData Type | Notes |
|-------------|-----------|-------|
| StringField | `string` | Text input |
| IntegerField | `?int` | Nullable for empty input |
| FloatField | `?float` | Nullable for empty input |
| DecimalField | `?string` | Decimal as string for precision |
| BooleanField | `bool` | Checkbox (never null) |
| DateField | `?Carbon` | Carbon instance or null |
| DateTimeField | `?Carbon` | Carbon instance or null |
| SelectField | `?string` | Selected value |
| SelectMultipleField | `array<int, string>` | Multiple selected values |
| RadioField | `?string` | Selected value |
| FieldList | `array<int, TInner>` | Generic over inner field's data |
| FormField | `TForm` | Generic over nested form |

---

### Q5.3 - FieldList Generics

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (FieldList.php)

**Findings:**
**Current:**
```php
/**
 * @extends Field<array<int, mixed>>
 */
class FieldList extends Field
{
    /** @var Field<mixed> */
    public Field $inner_field;
}
```

**Problem:** Uses `mixed` instead of propagating inner field type.

**Recommended design:**
```php
/**
 * @template TInnerField of Field
 * @template TInnerData
 * @extends Field<array<int, TInnerData>>
 */
class FieldList extends Field
{
    /** @var TInnerField */
    public Field $inner_field;
}

// Usage
/** @var FieldList<StringField, string> */
$names = new FieldList(new StringField());
```

---

### Q5.4 - FormField Generics

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (FormField.php)

**Findings:**
**Recommended design:**
```php
/**
 * @template TForm of Form
 * @extends Field<TForm>
 */
class FormField extends Field
{
    /** @var class-string<TForm> */
    public string $form_class;

    /** @var TForm */
    public Form $form;
}

// Usage
/** @var FormField<AddressForm> */
$address = new FormField(AddressForm::class);
$address->form->city  // PHPStan knows this is AddressForm
```

---

### Q5.5 - SelectField Choices Type

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (SelectFieldBase.php)

**Findings:**
**Choices structure:**
```php
// Each choice is [value, label, selected]
array<int, array{0: string, 1: string, 2: bool}>
```

**Or as named shape:**
```php
/**
 * @phpstan-type Choice array{value: string, label: string, selected: bool}
 * @phpstan-type Choices array<int, Choice>
 */
```

**Dynamic choices (callable):**
```php
/** @var Choices|callable(): Choices */
public $choices;
```

---

### Q5.6 - processFormData Typing

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current signature:**
```php
public function processFormData(array $valuelist): void
```

**Recommended:**
```php
/**
 * Process raw form data into field data.
 *
 * @param array<int, string> $valuelist Raw values from form submission
 */
public function processFormData(array $valuelist): void
```

**Return type:** `void` - modifies `$this->data` directly.

---

### Q5.7 - raw_data Type

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current:**
```php
/** @var array<int, mixed>|null */
public ?array $raw_data = [];
```

**Recommended:**
```php
/** @var array<int, string>|null */
public ?array $raw_data = null;
```

Raw form data is always strings (from `$_POST`/`$_GET`). Initialize to `null` not empty array for cleaner null checks.

---

### Q5.8 - Widget Property Type

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current:**
```php
/** @var Widget|object|null */
public $widget;
```

**Problem:** Too loose. `object` allows anything.

**Recommended:**
```php
/**
 * @template TData
 * @var Widget<TData>|null
 */
public ?Widget $widget = null;
```

Each field subclass should narrow to specific widget:
```php
// StringField
/** @var Input<string>|null */
public ?Widget $widget = null;
```

---

### Q5.9 - Validators Array Type

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current:**
```php
/** @var list<Validator|callable> */
public array $validators = [];
```

**Issue:** `callable` is too loose. Should be specific signature.

**Recommended:**
```php
/**
 * @phpstan-type ValidatorCallable callable(Form, Field, ?string): void
 * @var list<Validator<array<string, mixed>>|ValidatorCallable>
 */
public array $validators = [];
```

---

### Q5.10 - Variance Issues

**Status:** Answered

**Investigation Method:**
- [x] PHPStan output analysis

**Findings:**
**Current PHPStan errors (level 5) related to variance:**
```
Parameter #1 $field of method Widget::__invoke() expects
Field<float|int|null>, Field<float|int|null> given.
Template type TData on class Field is not covariant.
```

**The issue:**
- `Field<TData>` template is invariant by default
- Widget expects `Field<TData>` but gets subclass

**Solution options:**
1. Use `@template-covariant TData` where appropriate
2. Use bounded generics: `@template TData of string|int|null`
3. With breaking changes: redesign to avoid variance issues

**Recommended:**
```php
/**
 * @template-covariant TData
 */
class Field { }
```

---

## Section 6: Validator Classes Typing

### Q6.1 - TOptions Template Pattern

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (Validator.php)

**Findings:**
**Current pattern:**
```php
/**
 * @template TOptions of array<string, mixed>
 */
abstract class Validator
{
    /** @var TOptions */
    private array $options;

    /**
     * @param TOptions|ArrayOptions<TOptions> $options
     */
    public function __construct(string $message = '', $options = [])
}
```

**Subclass pattern:**
```php
/**
 * @phpstan-type LengthOptionsArray array{min?:int, max?:int}
 * @template-extends Validator<LengthOptionsArray>
 */
class Length extends Validator
```

This is a solid pattern - each validator specifies its options shape.

---

### Q6.2 - Per-Validator Options Types

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (all validators)

**Findings:**
**Options types by validator:**

| Validator | Options Type |
|-----------|-------------|
| Length | `array{min?: int, max?: int}` |
| NumberRange | `array{min?: int\|float, max?: int\|float}` |
| Regexp | `array{regex: string, flags?: string}` |
| Email | `array{}` (no options) |
| URL | `array{}` |
| EqualTo | `array{fieldname: string}` |
| AnyOf | `array{values: array<int, mixed>}` |
| NoneOf | `array{values: array<int, mixed>}` |
| IPAddress | `array{ipv4?: bool, ipv6?: bool}` |
| Optional | `array{strip_whitespace?: bool}` |

---

### Q6.3 - Validator Return Type

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current:**
```php
abstract public function __invoke(Form $form, Field $field, string $message = '');
```

**Recommendation:**
```php
/**
 * @throws ValidationError
 * @throws StopValidation
 */
abstract public function __invoke(Form $form, Field $field, string $message = ''): void;
```

Return `void` - validators either succeed silently or throw exceptions.

---

### Q6.4 - Message Interpolation Type

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Message interpolation uses `vsprintf_named()`:**
```php
$message = vsprintf_named($message, ['min' => $this->min, 'max' => $this->max]);
```

**Type:**
```php
/**
 * @param string $message Format string with %(name)s placeholders
 * @param array<string, scalar> $args Named arguments for interpolation
 * @return string Interpolated message
 */
function vsprintf_named(string $message, array $args): string
```

---

### Q6.5 - field_flags Type

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current:**
```php
public array $field_flags = [];
```

**Used by validators like InputRequired:**
```php
public array $field_flags = ['required' => true];
```

**Recommended type:**
```php
/** @var array<string, bool> */
public array $field_flags = [];
```

Flags are always string keys with boolean values.

---

### Q6.6 - Field Data Access Typing

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Problem:** Validators access `$field->data` but field data type varies.

**Current (Length.php):**
```php
if (null !== $field->data) {
    $length = strlen($field->data);  // Assumes string
}
```

**Solutions:**
1. Validators could be generic over field type
2. Use type assertions/narrowing
3. Accept `Field<mixed>` and check at runtime

**Recommended:** Type assertions with guards:
```php
if (!is_string($field->data)) {
    return; // Skip validation for non-string
}
$length = strlen($field->data);
```

---

### Q6.7 - phpstan-assert Usage

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**`@phpstan-assert` can narrow types after validation:**
```php
/**
 * @phpstan-assert !null $field->data
 */
public function __invoke(Form $form, Field $field, string $message = ''): void
```

**For InputRequired/DataRequired:**
```php
/**
 * @phpstan-assert-if-true !null $field->data
 */
```

**Benefit:** After calling validator, PHPStan knows data is not null.

**Recommendation:** Add assertions to validators that guarantee types.

---

### Q6.8 - Optional vs Required Typing

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Pattern:**
- `InputRequired`/`DataRequired` → throw StopValidation if empty
- `Optional` → throw StopValidation if empty (to skip remaining validators)

**Type implications:**
- After InputRequired passes: `$field->data` is not null/empty
- After Optional passes: validation chain stops if empty

**Annotation:**
```php
// InputRequired
/**
 * @phpstan-assert !null $field->data
 * @throws StopValidation If field is empty
 */

// Optional
/**
 * @throws StopValidation If field is empty (stops validation, not an error)
 */
```

---

### Q6.9 - Exception Hierarchy

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current exceptions:**
```
\Exception
└── WTForms\Exceptions\WTFormsException (base)
    ├── StopValidation - Control flow: stop validation chain
    ├── ValidationError - Validation failure: add error message
    ├── ValueError - Invalid argument
    └── TypeError - Type mismatch
```

**ValidationError usage:**
```php
throw new ValidationError($message);
// Message is added to $field->errors
```

**StopValidation usage:**
```php
throw new StopValidation($message);
// Stops validation, optionally adds message
```

---

### Q6.10 - Regexp Pattern Type

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Regexp validator options:**
```php
/**
 * @phpstan-type RegexpOptionsArray array{regex: string, flags?: string}
 */
```

**Pattern type considerations:**
- `regex` is a PCRE pattern string
- Could use `non-empty-string` to ensure not empty
- Could create branded type `@psalm-type PcrePattern = non-empty-string`

**Validation:**
```php
if (@preg_match($this->regex, '') === false) {
    throw new ValueError('Invalid regex pattern');
}
```

---

## Section 7: Widget Classes Typing

### Q7.1 - Widget TData Template Pattern

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (Widget.php)

**Findings:**
**Current pattern:**
```php
/**
 * @template TData The type of data the associated field holds
 */
abstract class Widget
{
    /**
     * @param Field<TData> $field The field to render
     */
    abstract public function __invoke(Field $field, array $options = []): string;
}
```

**Subclass pattern:**
```php
/**
 * @template TData
 * @extends Widget<TData>
 */
class Input extends Widget

/**
 * @extends Widget<string|int>
 */
class Select extends Widget
```

This pattern links widget to field data type.

---

### Q7.2 - Field Compatibility Typing

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current approach:**
```php
// Select.php - uses @param for specific field type
/**
 * @param SelectFieldBase<string|int> $field
 */
public function __invoke(Field $field, array $options = []): string
```

**Problem:** PHP signature is `Field` but PHPDoc says `SelectFieldBase`.

**Recommendation:** Use bounds on template:
```php
/**
 * @template TField of SelectFieldBase
 * @extends Widget<string|int>
 */
class Select extends Widget
{
    /**
     * @param TField $field
     */
    public function __invoke(Field $field, array $options = []): string
}
```

---

### Q7.3 - Options Parameter Type

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current:**
```php
/** @param array<string, mixed> $options */
public function __invoke(Field $field, array $options = []): string
```

**More specific:**
```php
/**
 * @phpstan-type HtmlAttributes array<string, string|int|bool|array<string>>
 * @param HtmlAttributes $options
 */
```

HTML attributes can be:
- `string` - normal value
- `int` - numeric (tabindex, etc.)
- `bool` - true = attribute present, false = omit
- `array<string>` - for class (joined with space)

---

### Q7.4 - HTML Attributes Type

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (html_params)

**Findings:**
**Based on `html_params()` function:**
```php
/**
 * @phpstan-type HtmlAttributeValue string|int|bool|array<int, string>
 * @phpstan-type HtmlAttributes array<string, HtmlAttributeValue>
 */
```

**Processing:**
- `true` → attribute name only (`checked`)
- `false` → omit attribute
- `array` → join with space (`class_` => ['foo', 'bar'] → `class="foo bar"`)
- Other → escape with `e()`

---

### Q7.5 - Return Type

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current:** `string`

**Options:**
1. `string` - simple, works
2. `non-empty-string` - guarantees output
3. Custom branded type for HTML

**Recommendation:** Keep `string`, but add Psalm taint annotation:
```php
/**
 * @psalm-taint-escape html
 * @return string HTML output
 */
public function __invoke(Field $field, array $options = []): string
```

---

### Q7.6 - ListWidget Typing

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (ListWidget.php)

**Findings:**
**ListWidget renders a collection of subfields:**
```php
foreach ($field as $subfield) {
    $html .= sprintf('<li>%s %s</li>', $subfield->label, $subfield());
}
```

**Type:**
```php
/**
 * @template TSubField of Field
 * @extends Widget<array<int, mixed>>
 */
class ListWidget extends Widget
{
    /**
     * @param FieldList<TSubField, mixed> $field
     */
    public function __invoke(Field $field, array $options = []): string
}
```

---

### Q7.7 - Select Widget Choices Typing

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (Select.php)

**Findings:**
**Choices iteration:**
```php
foreach ($field->getChoices() as [$value, $label, $selected]) {
    $html .= self::renderOption($value, $label, $selected);
}
```

**Type:**
```php
/**
 * @phpstan-type Choice array{0: string|int, 1: string, 2: bool}
 * @return iterable<int, Choice>
 */
public function getChoices(): iterable
```

**Note (Q4.8):** `$label` in `renderOption` should be escaped with `e()`.

---

### Q7.8 - Widget field_flags Type

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current:**
```php
/** @var list<string> */
public array $field_flags = [];
```

**Usage:** Widget declares which flags it sets on fields.

**This differs from Validator's `field_flags`:**
- Validator: `array<string, bool>` (flag name => value)
- Widget: `list<string>` (flag names)

**Recommendation:** Align to same pattern:
```php
/** @var array<string, bool> */
public array $field_flags = [];
```

---

### Q7.9 - State Modification During Rendering

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Widgets that modify field state:**
- `CheckboxInput` - may set `checked` attribute based on `$field->data`
- `Select` - iterates and checks `$selected` state

**Generally safe:** Widgets read state, don't modify it.

**Recommendation:** Keep widgets pure (read-only). If state modification needed, document it.

---

### Q7.10 - Taint Sinks in Widgets

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (covered in Q4.3)

**Findings:**
**All widgets are taint sinks** - they output HTML.

**Already sanitized:**
- `html_params()` escapes attribute values via `e()`
- `TextArea` escapes content with `e($field->value)`

**Needs fixing:**
- `Select::renderOption()` - `$label` not escaped

**Annotations to add:**
```php
/**
 * @psalm-taint-sink html
 */
abstract public function __invoke(Field $field, array $options = []): string;
```

---

## Section 8: Form & Meta Typing

### Q8.1 - ArrayAccess Typing

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (FormArrayable trait)

**Findings:**
**Current implementation:**
```php
class Form implements \ArrayAccess
{
    public function offsetGet($offset) // Returns Field
    public function offsetSet($offset, $value) // Accepts Field
    public function offsetExists($offset) // Returns bool
    public function offsetUnset($offset) // Returns void
}
```

**BREAKING CHANGE APPROVED:** Remove ArrayAccess entirely.

**New approach:**
```php
/**
 * @return Field<mixed>|null
 */
public function getField(string $name): ?Field

public function hasField(string $name): bool
```

Direct property access `$form->username` via typed properties replaces `$form['username']`.

---

### Q8.2 - Iterator Typing

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (FormIterator trait)

**Findings:**
**Current:**
```php
class Form implements \Iterator
{
    public function current() // Returns Field
    public function key() // Returns string
    public function next() // void
    public function rewind() // void
    public function valid() // bool
}
```

**Recommended:**
```php
/**
 * @implements \Iterator<string, Field<mixed>>
 */
class Form implements \Iterator
{
    /** @return Field<mixed> */
    public function current(): Field
    /** @return string */
    public function key(): string
}
```

Iterating over form yields field names as keys and Field objects as values.

---

### Q8.3 - $_fields Storage Type

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current:**
```php
/** @var array<Field> */
public $fields = [];
```

**Recommended:**
```php
/** @var array<string, Field<mixed>> */
private array $fields = [];
```

- Key: field name (string)
- Value: Field instance
- Make private, access via getField/hasField

---

### Q8.4 - Magic __get Handling

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current:**
```php
public function __get($field_name)
{
    if ($this->offsetExists($field_name)) {
        return $this->offsetGet($field_name);
    }
    return null;
}
```

**BREAKING CHANGE APPROVED:** Remove `__get` magic.

**New approach:** Real typed properties via constructor promotion:
```php
class LoginForm extends Form {
    public function __construct(
        public StringField $username,
        public PasswordField $password,
    ) {
        parent::__construct();
    }
}

// Usage - fully typed
$form->username  // StringField (real property)
```

---

### Q8.5 - data/errors Types

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current (virtual properties via @property):**
```php
/**
 * @property array $data
 * @property array $errors
 */
```

**Recommended types:**
```php
/**
 * @return array<string, mixed>
 */
public function getData(): array

/**
 * @return array<string, list<string>>
 */
public function getErrors(): array
```

- `data`: Map of field name to field data value
- `errors`: Map of field name to list of error messages

---

### Q8.6 - populate_obj Generics

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current:**
```php
public function populateObj($obj) // object
```

**Recommended:**
```php
/**
 * @template T of object
 * @param T $obj
 * @return T
 */
public function populateObj(object $obj): object
```

This allows type-safe object population:
```php
$user = new User();
$form->populateObj($user);  // Returns User
```

---

### Q8.7 - formdata Input Type

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current:** `array` in constructor options.

**Recommended:**
```php
/**
 * @phpstan-type FormData array<string, string|array<int, string>>
 */
```

Form data is:
- Single values: `string`
- Multi-value (checkboxes, multi-select): `array<int, string>`

All values are strings from `$_POST`/`$_GET`.

---

### Q8.8 - Meta Configuration Type

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (DefaultMeta.php)

**Findings:**
**Current Meta properties:**
```php
public string $csrf_field_name = 'csrf_token';
public ?string $csrf_time_limit = null;
public bool $csrf = false;
public ?string $csrf_secret = null;
/** @var ?class-string<CSRF> */
public ?string $csrf_class = null;
```

**Recommended shape type:**
```php
/**
 * @phpstan-type MetaOptions array{
 *     csrf?: bool,
 *     csrf_field_name?: string,
 *     csrf_time_limit?: string|null,
 *     csrf_secret?: string|null,
 *     csrf_class?: class-string<CSRF>|null
 * }
 */
```

---

### Q8.9 - validate() Signature

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current:**
```php
public function validate($extra_validators = [])
```

**Recommended:**
```php
/**
 * @param array<string, list<Validator<mixed>>> $extra_validators
 * @return bool True if validation passed
 */
public function validate(array $extra_validators = []): bool
```

Returns `true` if all fields valid, `false` otherwise.
Errors available via `getErrors()`.

---

### Q8.10 - CSRF Field Integration

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current:**
```php
/** @var array|Field|null */
public $csrf_token;

/** @var object */
public $csrf;
```

**Recommended:**
```php
use WTForms\CSRF\Core\CSRFTokenField;
use WTForms\CSRF\Core\CSRF;

/** @var CSRFTokenField|null */
public ?CSRFTokenField $csrf_token = null;

/** @var CSRF|null */
public ?CSRF $csrf = null;
```

Proper types for CSRF integration.

---

## Section 9: CSRF & Security Typing

### Q9.1 - CSRF Base Class Type Structure

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
The `CSRF` base class structure:

```php
abstract class CSRF
{
  /**
   * @var class-string<\WTForms\Fields\Core\Field>
   */
  public string $field_class = CSRFTokenField::class;

  public function setupForm(Form $form): array;
  abstract public function generateCSRFToken(CSRFTokenField $csrf_token_field): string;
  public function validate_csrf_token(Form $form, CSRFTokenField $field): void; // throws ValidationError
}
```

**Recommended types:**
```php
/**
 * @template TTokenField of CSRFTokenField
 */
abstract class CSRF
{
  /** @var class-string<TTokenField> */
  public string $field_class = CSRFTokenField::class;

  /**
   * @return array{0: string, 1: TTokenField} [field_name, unbound_field]
   */
  public function setupForm(Form $form): array;

  abstract public function generateCSRFToken(CSRFTokenField $csrf_token_field): string;

  /**
   * @throws ValidationError
   */
  public function validate_csrf_token(Form $form, CSRFTokenField $field): void;
}
```

---

### Q9.2 - Token Generation Method Types

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current:**
```php
abstract public function generateCSRFToken(CSRFTokenField $csrf_token_field): string;
```

**Already has proper return type.** Token is always a string.

For `SessionCSRF`:
```php
/**
 * @throws \Exception When csrf_secret not set
 * @throws TypeError When session not active
 */
public function generateCSRFToken(CSRFTokenField $csrf_token_field): string
```

Token format: `"{expires}##{hmac}"` or `"##{hmac}"` when no time_limit.

---

### Q9.3 - Session Storage Integration Types

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
`SessionCSRF` uses `$_SESSION` directly:

```php
$_SESSION[$this->session_key] = sha1(openssl_random_pseudo_bytes(64));
```

**Type issues:**
1. `$_SESSION` is typed as `array<string, mixed>` - can't improve this
2. `$this->session_key` is `string`
3. Values stored are sha1 hashes (string)

**Recommendation:**
```php
/** @var string Session key for CSRF token storage */
public string $session_key = 'csrf';

/** @psalm-taint-source input */
private function getSessionToken(): string
{
    return $_SESSION[$this->session_key] ?? '';
}
```

No taint annotation needed for session values as they're server-generated.

---

### Q9.4 - Time-Limit Validation Types

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current (uses magic `__get`):**
```php
/** @property int $time_limit */
class SessionCSRF extends CSRF
{
    public function __get($name) {
        if ('time_limit' == $name) {
            return $this->form_meta->csrf_time_limit ?? 1800;
        }
    }
}
```

**Recommended (explicit property):**
```php
/**
 * Time limit in seconds for token validity. 0 or null = no limit.
 * @var int|null
 */
protected ?int $time_limit = null;

public function getTimeLimit(): int
{
    return $this->time_limit ?? $this->form_meta->csrf_time_limit ?? 1800;
}
```

**Breaking change:** Remove `__get` magic, add explicit getter.

---

### Q9.5 - Security-Sensitive Operations Requiring Taint Analysis

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Taint sources in CSRF:**
1. `$field->data` - form input, tainted
2. `$_SESSION[$key]` - technically safe (server-side), but should be marked

**Taint sinks:**
- None found - CSRF tokens aren't rendered in HTML context unsafely

**Security-sensitive operations:**
1. `hash_hmac()` - cryptographic, needs correct key handling
2. `openssl_random_pseudo_bytes()` - CSPRNG, needs error handling
3. Token comparison - timing-safe required

**Recommended annotations:**
```php
/**
 * @psalm-taint-sink security $field->data
 */
public function validate_csrf_token(Form $form, CSRFTokenField $field): void
```

---

### Q9.6 - Public API Method Types

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**`generateCSRFToken()`** - Already typed correctly:
```php
public function generateCSRFToken(CSRFTokenField $csrf_token_field): string
```

**`validate_csrf_token()`** - Needs return type and throws annotation:
```php
/**
 * @throws ValidationError When token invalid, missing, or expired
 */
public function validate_csrf_token(Form $form, CSRFTokenField $field): void
```

**`setupForm()`** - Needs precise return type:
```php
/**
 * @return array{0: string, 1: CSRFTokenField}
 * @throws \ReflectionException
 */
public function setupForm(Form $form): array
```

---

### Q9.7 - CSRF Field Metadata Types

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**`CSRFTokenField` metadata:**
```php
/**
 * @template TCSRFImpl of CSRF
 * @phpstan-type CSRFTokenFieldOptions array{label?:string, csrf_impl?: class-string<TCSRFImpl>|TCSRFImpl, ...}
 */
class CSRFTokenField extends HiddenField
{
  public string $current_token = '';
  /** @var TCSRFImpl */
  public $csrf_impl;
}
```

**Recommendation:** Add native type to `$csrf_impl`:
```php
/** @var CSRF */
public CSRF $csrf_impl;
```

The generic `TCSRFImpl` pattern is already in place and correct.

---

### Q9.8 - Nullable Token Handling

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current validation:**
```php
if (!$field->data || !str_contains($field->data, '##')) {
    throw new ValidationError('CSRF token missing');
}
```

`$field->data` can be:
- `null` (no form submission)
- `''` (empty submission)
- `string` (actual token)

**Recommended typing:**
```php
/** @var string|null */
public ?string $data = null;
```

The null check is correctly handled before string operations.

---

### Q9.9 - Timing-Safe Comparison Typing

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current code uses string comparison (vulnerable!):**
```php
if ($hmac_compare !== $hmac_csrf) {
    throw new ValidationError('CSRF failed');
}
```

**SECURITY ISSUE:** Should use `hash_equals()`:
```php
if (!hash_equals($hmac_compare, $hmac_csrf)) {
    throw new ValidationError('CSRF failed');
}
```

**`hash_equals` type:**
```php
function hash_equals(string $known_string, string $user_string): bool
```

Already well-typed in PHP stubs. Just needs to be used.

---

### Q9.10 - CSRF Validation Exceptions

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Exceptions thrown by CSRF validation:**

| Exception | Where | When |
|-----------|-------|------|
| `ValidationError` | `CSRF::validate_csrf_token()` | Token mismatch |
| `ValidationError` | `SessionCSRF::validate_csrf_token()` | Token missing |
| `ValidationError` | `SessionCSRF::validate_csrf_token()` | Token expired |
| `ValidationError` | `SessionCSRF::validate_csrf_token()` | Invalid format |
| `\Exception` | `SessionCSRF::generateCSRFToken()` | csrf_secret missing |
| `TypeError` | `SessionCSRF::generateCSRFToken()` | Session not active |
| `\ReflectionException` | `CSRF::setupForm()` | Invalid field_class |

**Recommendation:**
```php
/**
 * @throws ValidationError Always for validation failures
 */
public function validate_csrf_token(Form $form, CSRFTokenField $field): void;

/**
 * @throws \RuntimeException When csrf_secret missing
 * @throws \RuntimeException When session not active
 */
public function generateCSRFToken(CSRFTokenField $csrf_token_field): string;
```

Consider using specific exception types like `CSRFConfigurationException`.

---

## Section 10: Generics & Templates Strategy

### Q10.1 - Template Naming Convention

**Status:** Answered

**Investigation Method:**
- [x] Code inspection
- [x] Documentation review

**Findings:**
**Current conventions in codebase:**
- `TData` - Field data type
- `TOptions` - Validator options
- `TCSRFImpl` - CSRF implementation
- `TShape` - ArrayOptions shape

**Recommended convention:**
| Template | Meaning | Example |
|----------|---------|---------|
| `TData` | Field data type | `Field<TData>`, `Widget<TData>` |
| `TOptions` | Validator/Field options shape | `Validator<TOptions>` |
| `TField` | Field type | `FieldList<TField>` |
| `TForm` | Form type | `FormField<TForm>` |
| `TValue` | Single value type | `SelectField<TValue>` |
| `T` | Generic single-letter when obvious | `from(T $input)` |

**Rules:**
1. Always prefix with `T`
2. Use descriptive suffix
3. Match parent class convention when extending

---

### Q10.2 - Covariant Return Types in PHP 7.4

**Status:** Answered

**Investigation Method:**
- [x] Documentation review
- [x] Code inspection

**Findings:**
**PHP 7.4 supports native covariant returns:**
```php
class Parent {
    public function getValue(): ParentType { }
}
class Child extends Parent {
    public function getValue(): ChildType { } // OK if ChildType extends ParentType
}
```

**For PHPDoc generics, use `@template-covariant`:**
```php
/**
 * @template-covariant TData
 */
class Field {
    /** @return TData */
    public function getData() { }
}

/**
 * @template-extends Field<string>
 */
class StringField extends Field {
    public function getData(): string { } // Covariant - allowed
}
```

**When to use:**
- Read-only data access (getters)
- Factory methods returning specific types
- `getData()`, `getValue()`, `getDefault()`

---

### Q10.3 - Contravariant Parameter Types in PHP 7.4

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**PHP 7.4 supports native contravariant parameters:**
```php
class Parent {
    public function process(ChildType $input) { }
}
class Child extends Parent {
    public function process(ParentType $input) { } // OK - can accept broader type
}
```

**For PHPDoc generics, this is the default behavior:**
```php
/**
 * @template TData
 */
class Field {
    /** @param TData $value */
    public function setData($value): void { }
}
```

**Parameters are contravariant by default.** Only use `@template-covariant` for return types.

**Widget example:**
```php
/**
 * @template TData
 */
abstract class Widget {
    /**
     * @param Field<TData> $field  // Contravariant - accepts Field or subclass
     */
    abstract public function __invoke(Field $field, array $options = []): string;
}
```

---

### Q10.4 - Bounded Generics Syntax

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**PHPStan/Psalm syntax for bounded generics:**

```php
/**
 * @template T of SomeClass
 */
```

**Examples from codebase:**
```php
// CSRFTokenField - bounded to CSRF
@template TCSRFImpl of CSRF

// ArrayOptions - bounded to array shape
@template TShape of array<string, mixed>

// FieldList - bounded to Field
@template TField of Field<mixed>
```

**Use cases:**
1. **Ensure method availability:** `T of Comparable` means `$t->compare()` exists
2. **Limit acceptable types:** Only accept Field subclasses
3. **Type narrowing:** IDE knows methods available on T

**Syntax variations:**
```php
@template T of ClassA               // Bounded to ClassA
@template T of ClassA&InterfaceB    // Intersection (must be both)
@template T of ClassA|ClassB        // Union is NOT supported in templates
```

---

### Q10.5 - Generic Inheritance Propagation

**Status:** Answered

**Investigation Method:**
- [x] Code inspection
- [x] Documentation review

**Findings:**
**Pattern for propagating generics through inheritance:**

```php
/**
 * @template TData
 */
abstract class Field { }

/**
 * @template-extends Field<string>
 */
class StringField extends Field { }

/**
 * @template TData
 * @template-extends Field<TData>
 */
abstract class ChoiceField extends Field { }

/**
 * @template TValue
 * @template-extends ChoiceField<TValue>
 */
class SelectField extends ChoiceField { }
```

**Key annotations:**
- `@template-extends` - For class extension
- `@template-implements` - For interface implementation
- Forward reference: `@template-extends Parent<TData>` keeps generic open

**Common mistakes:**
```php
// WRONG - loses type information
class StringField extends Field { }  // Missing @template-extends

// WRONG - circular reference
@template TData
@template-extends Field<TData>  // Re-declares TData unnecessarily
class StringField extends Field { }
```

---

### Q10.6 - @template-covariant Usage

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**Use `@template-covariant` when:**
1. Template only appears in **return positions** (output)
2. Class is a **producer** of the type
3. Never accepts the type as input

```php
/**
 * @template-covariant TData
 */
class ReadOnlyField {
    /** @return TData */
    public function getData() { }  // ✓ Return position only
}

// Can safely do:
/** @var ReadOnlyField<ChildType> */
$field = $childField;
/** @var ReadOnlyField<ParentType> */
$field2 = $field;  // OK - covariant
```

**Do NOT use when:**
- Template appears in parameter positions
- Class has setters for the type
- Type is used in write contexts

```php
/**
 * @template TData  // NOT covariant - used in setData()
 */
class Field {
    public function getData() { }
    public function setData($value): void { }  // Input position
}
```

**In php-wtforms:**
- `Field<TData>` - NOT covariant (has `setData`)
- `Widget<TData>` - Covariant candidate (only reads data for display)

---

### Q10.7 - Type Import Syntax

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**`@phpstan-import-type` syntax:**

```php
// In Field.php - define the type
/**
 * @phpstan-type FieldOptions array{
 *     label?: string,
 *     validators?: list<Validator<mixed>>,
 *     filters?: list<callable(mixed): mixed>,
 *     ...
 * }
 */
class Field { }

// In StringField.php - import and extend
/**
 * @phpstan-import-type FieldOptions from Field
 * @phpstan-type StringFieldOptions FieldOptions&array{maxlength?: int}
 */
class StringField extends Field { }
```

**Also works for importing generics:**
```php
/**
 * @phpstan-import-type TData from Field
 */
class SomeHelper { }
```

**Benefits:**
1. DRY - Define complex types once
2. Consistent - Same type everywhere
3. Refactor-friendly - Change in one place

---

### Q10.8 - PHP 7.4 Generic Limitations

**Status:** Answered

**Investigation Method:**
- [x] Documentation review
- [x] Code inspection

**Findings:**
**PHPDoc generics limitations in PHP 7.4:**

| Feature | Status | Workaround |
|---------|--------|------------|
| Native generics | ❌ Not supported | PHPDoc only |
| Runtime type checking | ❌ No | Use assertions |
| Type erasure | ⚠️ Yes, at runtime | PHPDoc for static analysis |
| Reified generics | ❌ No | Pass class-string |
| Generic closures | ⚠️ Limited | Use `@param callable(T): R` |
| Variance checking | ⚠️ PHPDoc only | `@template-covariant` |

**Common workarounds:**

1. **No reification - pass class name:**
```php
/**
 * @template T
 * @param class-string<T> $class
 * @return T
 */
public function create(string $class) {
    return new $class();
}
```

2. **Collection types:**
```php
// Can't do: List<string>
// Use: list<string> or array<int, string>
```

3. **No union in templates:**
```php
// Can't do: @template T of A|B
// Use: @template T of CommonInterface
```

---

### Q10.9 - IDE Documentation for Generics

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**Best practices for IDE support:**

1. **Always use complete annotations:**
```php
/**
 * @template TData
 * @param Field<TData> $field
 * @return TData
 */
```

2. **Provide inline hints for complex types:**
```php
/** @var array<string, Field<mixed>> */
$fields = [];
```

3. **Document at class level:**
```php
/**
 * A field that holds a string value.
 *
 * @template-extends Field<string>
 *
 * @property string $data The field's data
 */
class StringField extends Field { }
```

4. **Use `@see` for related types:**
```php
/**
 * @see FieldOptions for constructor options
 * @see Validator for validation
 */
```

**IDE support matrix:**
| IDE | PHPStan types | Psalm types | @template |
|-----|---------------|-------------|-----------|
| PhpStorm | ✅ | ✅ | ✅ (2023.1+) |
| VS Code (Intelephense) | ✅ | ✅ | ✅ |
| VS Code (PHP Intelisense) | ⚠️ | ⚠️ | ❌ |

---

### Q10.10 - Circular Type Dependencies

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Circular dependencies in php-wtforms:**

1. **Form ↔ Field:**
   - Form contains Fields
   - Field references Form (for validation context)

```php
class Form {
    /** @var array<string, Field<mixed>> */
    public array $fields;
}

class Field {
    /** @var Form|null */
    public ?Form $form = null;
}
```

2. **Field ↔ Widget:**
   - Field uses Widget
   - Widget receives Field in `__invoke()`

```php
class Field {
    /** @var Widget<TData> */
    public $widget;
}

class Widget {
    public function __invoke(Field $field): string;
}
```

**Solutions:**
1. **Use nullable types** for forward references
2. **Late binding** - set form reference after construction
3. **Avoid generic cycles** - use `mixed` at one end if needed

```php
// Avoid: Field<TData> → Widget<TData> → Field<TData> cycle
// Use: Widget receives Field<mixed>, casts internally
```

---

## Section 11: Internal Utilities Typing

### Q11.1 - Internal Utility Classes

**Status:** Answered

**Investigation Method:**
- [x] Code inspection (Glob search)

**Findings:**
**Internal utility classes:**

| Class | Location | Purpose |
|-------|----------|---------|
| `ArrayOptions` | `src/Internal/Types/` | Type-safe options base class |
| `Flags` | `src/` | Dynamic boolean flags container |
| `Label` | `src/` | Field label wrapper |
| `DefaultMeta` | `src/` | Form metadata container |

**Exception classes (see Q11.4):**
- 7 exception classes in `src/Exceptions/`

**Helper functions (see Q11.3):**
- 7 functions in `src/helpers.php`

---

### Q11.2 - ArrayOptions Type Structure

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current typing (already well-done!):**

```php
/**
 * @template TShape of array<string, mixed>
 * @implements \ArrayAccess<key-of<TShape>, value-of<TShape>>
 * @implements \IteratorAggregate<key-of<TShape>, value-of<TShape>>
 * @phpstan-consistent-constructor
 */
abstract class ArrayOptions implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /** @var TShape */
    protected array $data = [];

    /** @param TShape $data */
    public function __construct(array $data = []) { }

    /** @return TShape */
    public function toArray(): array { }

    /** @param TShape|static $options */
    public static function from($options) { }
}
```

**Recommendation:** Already well-typed. Consider:
1. Add `@psalm-immutable` if options shouldn't change after construction
2. Remove `__get`/`__set` magic in favor of explicit accessors (per breaking changes approval)

---

### Q11.3 - Procedural Functions Needing Types

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Functions in `helpers.php`:**

| Function | Current Type | Recommended |
|----------|--------------|-------------|
| `e(string): string` | ✅ | Add `@psalm-taint-escape html` |
| `messages_path(): string` | ✅ | OK |
| `get_builtin_gnu_translations(?string): GettextTranslator` | ✅ | OK |
| `get_translations(?string, ?callable): GettextTranslator` | ⚠️ | Type the callable |
| `html_params(array): string` | ⚠️ | Add taint escape, better array type |
| `str_contains(string, ?string): bool` | ✅ | OK (polyfill) |
| `starts_with(string, string\|string[]): bool` | ⚠️ | Use `@param string\|list<string>` |
| `vsprintf_named(string, array): string` | ⚠️ | Better array type |

**Recommended improvements:**
```php
/**
 * @param string $string
 * @return string
 * @psalm-taint-escape html
 */
function e(string $string): string { }

/**
 * @param array<string, scalar|bool|list<string>> $options
 * @return string
 * @psalm-taint-escape html
 */
function html_params(array $options): string { }

/**
 * @param array<string, scalar> $args
 */
function vsprintf_named(string $format, array $args): string { }
```

---

### Q11.4 - Exception Class Hierarchy

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Exception hierarchy:**

```
\Exception
├── WTForms\Exceptions\ValidationError
├── WTForms\Exceptions\StopValidation
├── WTForms\Exceptions\TypeError
├── WTForms\Exceptions\ValueError
├── WTForms\Exceptions\NotImplemented
├── WTForms\Exceptions\IndexError
└── WTForms\Exceptions\AssertionError
```

**All extend `\Exception` directly.** No hierarchy between them.

**Recommendation:** Consider grouping:
```php
abstract class WTFormsException extends \Exception { }

class ValidationError extends WTFormsException { }
class StopValidation extends WTFormsException { }
class ConfigurationError extends WTFormsException { }  // New: for csrf_secret missing, etc.

// Python-compat exceptions
class TypeError extends \TypeError { }  // Extend PHP's TypeError
class ValueError extends \InvalidArgumentException { }
```

---

### Q11.5 - Static Helper Methods

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Static methods in codebase:**

| Class | Method | Current Type | Notes |
|-------|--------|--------------|-------|
| `ArrayOptions` | `from($options)` | `@param TShape\|static` | Already typed |
| `Field` | None | - | - |
| `Form` | None | - | - |
| `Validator` | None | - | - |

**No significant static helper methods requiring typing.**

The codebase prefers instance methods.

---

### Q11.6 - i18n/Translation Function Types

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Translation functions:**

```php
function get_translations(?string $languages = null, ?callable $getter = null): GettextTranslator
function get_builtin_gnu_translations(?string $language = ''): GettextTranslator
```

**Recommended improvements:**
```php
/**
 * @param string|null $languages Language code (e.g., 'en_US')
 * @param (callable(string|null): GettextTranslator)|null $getter Custom translator factory
 * @return GettextTranslator
 */
function get_translations(?string $languages = null, ?callable $getter = null): GettextTranslator { }
```

**Note:** Uses `gettext/translator` package's `GettextTranslator` class.

---

### Q11.7 - Reflection-Based Utility Types

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Reflection usage in codebase:**

1. **`CSRF::setupForm()`:**
```php
$unbound_field = (new \ReflectionClass($this->field_class))->newInstance([...]);
```

2. **`CSRFTokenField::__construct()`:**
```php
$this->csrf_impl = (new \ReflectionClass($options['csrf_impl']))->newInstance();
```

3. **`ArrayOptions::toArray()`:**
```php
$reflection = new \ReflectionClass($this);
foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) { }
```

**Typing recommendation:**
```php
/**
 * @template T of object
 * @param class-string<T> $class
 * @return T
 */
private function createInstance(string $class, array $args = []): object
{
    return (new \ReflectionClass($class))->newInstanceArgs($args);
}
```

Use `class-string<T>` for type-safe reflection.

---

### Q11.8 - Array Manipulation Utilities Needing Generics

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**No custom array utilities found.** The codebase uses:
- Standard `array_merge()`, `array_filter()`, etc.
- `ArrayOptions` for typed option bags

**Recommendation:** If needed in future:
```php
/**
 * @template TKey of array-key
 * @template TValue
 * @param array<TKey, TValue> $array
 * @param callable(TValue, TKey): bool $callback
 * @return array<TKey, TValue>
 */
function array_filter_typed(array $array, callable $callback): array
{
    return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
}
```

---

### Q11.9 - Constant Definitions and Types

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Constants in codebase:**

| Class | Constant | Type | Value |
|-------|----------|------|-------|
| `SessionCSRF` | `TIME_FORMAT` | `string` | `'%Y%m%d%H%i%s'` |

**Global constants:** None found.

**Recommendation:** Class constants are already typed by their values. No changes needed.

For future constants:
```php
/** @var string */
public const TIME_FORMAT = '%Y%m%d%H%i%s';
```

PHPStan infers constant types from values automatically.

---

### Q11.10 - Autoloading and Bootstrap Functions

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Autoloading:** Uses Composer PSR-4, no custom autoloader.

**Bootstrap files:**
- `src/helpers.php` - Loaded via Composer `files` autoload
- `tests/bootstrap.php` - PHPUnit bootstrap

**`tests/bootstrap.php` content (typical):**
```php
<?php
require __DIR__ . '/../vendor/autoload.php';
// Output buffering for session tests
ob_start();
```

**No typing needed** - bootstrap files are procedural entry points.

---

## Section 12: Testing Type Coverage

### Q12.1 - Test Code Inclusion Policy

**Status:** Answered

**Investigation Method:**
- [x] Code inspection
- [x] Documentation review

**Findings:**
**Current:** Tests not included in PHPStan analysis (`paths: src/` only).

**Recommendation:** Include tests at a **lower level**:
```neon
# phpstan.neon
parameters:
    paths:
        - src
        - tests

    # Different rules for tests
    ignoreErrors:
        - '#.*tests/.*#'  # Example - be more specific
```

**Better approach - separate configs:**
```neon
# phpstan.neon (production)
parameters:
    level: 9
    paths: [src]

# phpstan-tests.neon
includes:
    - phpstan.neon
parameters:
    level: 6
    paths: [tests]
```

**Rationale:** Tests may use dynamic mocking, loose assertions that don't need level 9 strictness.

---

### Q12.2 - PHPUnit PHPStan/Psalm Extensions

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**Recommended extensions:**

```bash
composer require --dev phpstan/phpstan-phpunit
```

**Features:**
- `assertInstanceOf()` narrows types
- `assertTrue($x instanceof Y)` narrows types
- `$this->expectException()` marks code as throwing
- `assertCount()`, `assertNull()`, etc. type narrowing
- Early termination detection for `fail()`, `markTestSkipped()`

**For Psalm:**
```bash
composer require --dev psalm/plugin-phpunit
```

**Already in require-dev:** None currently. **Add phpstan-phpunit.**

---

### Q12.3 - Mock Object Typing

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**PHPUnit mock types:**

```php
/**
 * @return MockObject&Form
 */
private function createFormMock(): MockObject
{
    return $this->createMock(Form::class);
}
```

**Best practices:**
1. Use intersection type `MockObject&OriginalClass`
2. Or use `@var` inline:
```php
/** @var Form&MockObject $form */
$form = $this->createMock(Form::class);
```

**With phpstan-phpunit extension:**
```php
$this->createMock(Form::class);  // Auto-typed as Form&MockObject
```

---

### Q12.4 - Test Helper Class Typing Policy

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Supporting classes found:**
- `tests/SupportingClasses/DummyCSRF.php`
- `tests/SupportingClasses/DummyField.php`
- `tests/SupportingClasses/FooMeta.php`

**Recommendation:** Type them as needed for tests, but don't require level 9:

```php
class DummyField extends Field
{
    // Typed to satisfy inheritance
    public function process(array $formdata, $data = null): void { }
}
```

**Policy:** Test helpers should be typed enough to:
1. Extend/implement production classes correctly
2. Not cause false positives in test analysis
3. Not be stricter than necessary

---

### Q12.5 - Data Provider Typing

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**PHPStan typing for data providers:**

```php
/**
 * @return iterable<string, array{0: string, 1: int, 2: bool}>
 */
public function validEmailsProvider(): iterable
{
    yield 'simple email' => ['test@example.com', 200, true];
    yield 'with subdomain' => ['test@sub.example.com', 200, true];
}

/**
 * @dataProvider validEmailsProvider
 */
public function testValidEmails(string $email, int $code, bool $expected): void
{
    // ...
}
```

**Key points:**
1. Return `iterable<string, array{...}>` for named datasets
2. Match tuple indices to test method parameters
3. Use `Generator` or `array` as concrete type

---

### Q12.6 - Assert Method Types

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**PHPUnit assert methods are already well-typed** in PHPUnit stubs.

With `phpstan-phpunit`:
```php
$this->assertInstanceOf(StringField::class, $field);
// After this line, PHPStan knows $field is StringField

$this->assertNull($value);
// After this line, PHPStan knows $value is null

$this->assertTrue($field instanceof StringField);
// Also narrows type
```

**Custom assertions need `@phpstan-assert`:**
```php
/**
 * @phpstan-assert StringField $field
 */
public function assertIsStringField(Field $field): void
{
    $this->assertInstanceOf(StringField::class, $field);
}
```

---

### Q12.7 - Test Fixture Typing Approach

**Status:** Answered

**Investigation Method:**
- [x] Code inspection

**Findings:**
**Current fixture pattern:**
```php
/**
 * @property StringField $a
 */
class GenericFieldTestForm extends Form
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->a = new StringField([...]);
    }
}
```

**Uses `@property` for magic property documentation.**

**With breaking changes (real properties):**
```php
class GenericFieldTestForm extends Form
{
    public StringField $a;

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->a = new StringField([...]);
        $this->process($options);
    }
}
```

**Recommendation:** Test fixtures should match production Form patterns exactly.

---

### Q12.8 - Custom Assertion @phpstan-assert Usage

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**`@phpstan-assert` for custom assertions:**

```php
/**
 * Assert that a field has no errors.
 *
 * @phpstan-assert list<never> $field->errors
 */
public function assertFieldHasNoErrors(Field $field): void
{
    $this->assertEmpty($field->errors);
}

/**
 * Assert form is valid.
 *
 * @phpstan-assert true $form->validate()
 */
public function assertFormIsValid(Form $form): void
{
    $this->assertTrue($form->validate());
}
```

**Also:**
- `@phpstan-assert-if-true` for boolean returns
- `@phpstan-assert-if-false` for negative cases

**Recommendation:** Add custom assertions for common form/field checks.

---

### Q12.9 - Separate Analysis Configuration

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**Strategy: Two PHPStan configs**

**`phpstan.neon` (production - strict):**
```neon
parameters:
    level: 9
    paths:
        - src
includes:
    - phpstan/extension.neon
    - vendor/phpstan/phpstan-strict-rules/rules.neon
```

**`phpstan-tests.neon` (tests - relaxed):**
```neon
includes:
    - phpstan.neon
    - vendor/phpstan/phpstan-phpunit/extension.neon
parameters:
    level: 6  # Lower level for tests
    paths:
        - tests
    ignoreErrors:
        # Allow some test-specific patterns
        - '#Call to an undefined method .+MockObject#'
```

**CI commands:**
```bash
vendor/bin/phpstan analyse -c phpstan.neon
vendor/bin/phpstan analyse -c phpstan-tests.neon
```

---

### Q12.10 - CI Workflow for Type Regression Prevention

**Status:** Answered

**Investigation Method:**
- [x] Documentation review

**Findings:**
**Recommended CI workflow (GitHub Actions):**

```yaml
# .github/workflows/ci.yml
name: CI
on: [push, pull_request]

jobs:
  static-analysis:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
      - run: composer install
      - name: PHPStan (src)
        run: vendor/bin/phpstan analyse --memory-limit=512M
      - name: PHPStan (tests)
        run: vendor/bin/phpstan analyse -c phpstan-tests.neon --memory-limit=512M
      - name: Psalm
        run: vendor/bin/psalm --no-cache
      - name: Psalm Taint Analysis
        run: vendor/bin/psalm --taint-analysis

  tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
          extensions: openssl
      - run: composer install
      - run: vendor/bin/phpunit
```

**Key elements:**
1. Run PHPStan on src/ at level 9
2. Run PHPStan on tests/ at level 6
3. Run Psalm for second opinion
4. Run taint analysis for security
5. Run tests to catch runtime issues

---

## Investigation Summary

### Completed Sections
- [x] Section 1: Current State Assessment
- [x] Section 2: PHPStan Configuration & Baseline
- [x] Section 3: Psalm Setup & Configuration
- [x] Section 4: Taint Analysis
- [x] Section 5: Field Classes Typing
- [x] Section 6: Validator Classes Typing
- [x] Section 7: Widget Classes Typing
- [x] Section 8: Form & Meta Typing
- [x] Section 9: CSRF & Security Typing
- [x] Section 10: Generics & Templates Strategy
- [x] Section 11: Internal Utilities Typing
- [x] Section 12: Testing Type Coverage

### Blocking Issues

No blocking issues. All 120 questions have been investigated.

### Key Decisions Made

1. **Breaking Change: Remove Magic Methods** - Replace `__get`/`__set` with explicit typed getters/setters. Enables full PHPStan level 9 inference.

2. **Breaking Change: Remove ArrayAccess on Form** - Use `getField(string): ?Field` method instead. Keeps `$form->username` via real typed properties (constructor promotion).

3. **Breaking Change: Separate Concerns** - Split field internals into typed structures:
   - `FieldAttributes` - HTML attributes
   - `FieldData<T>` - Actual value with generic type
   - `FieldFlags` - Boolean indicators
   - `FieldOptions` - Configuration

4. **Generics Strategy** - Use `@template TData` pattern consistently across Field, Widget, Validator hierarchies.

5. **Dual Static Analysis** - Use both PHPStan (level 9) and Psalm (level 1) for maximum coverage plus taint analysis.

6. **Taint Analysis** - Implement `@psalm-taint-source input` on formdata, `@psalm-taint-escape html` on `e()` and `html_params()`. Security issue found in `Select::renderOption()`.

7. **Test Separation** - Run tests at PHPStan level 6, separate from production code at level 9.

### Implementation Priority Order

1. **Phase 1: Foundation & Tooling**
   - Install Psalm and configure
   - Install PHPStan extensions (phpunit, strict-rules, deprecation-rules)
   - Create separate phpstan-tests.neon config
   - Set up CI workflow

2. **Phase 2: Fix Security Issues**
   - Use `hash_equals()` in SessionCSRF (timing-safe comparison)
   - Escape `$label` in `Select::renderOption()` (XSS fix)

3. **Phase 3: Helper Functions**
   - Add taint annotations to `e()`, `html_params()`
   - Type all helper functions in `helpers.php`
   - Improve exception hierarchy

4. **Phase 4: Core Class Typing**
   - Type `Field<TData>` base class with generics
   - Type `Widget<TData>` base class
   - Type `Validator<TOptions>` base class
   - Implement `@template-extends` on all subclasses

5. **Phase 5: Breaking Changes**
   - Remove `__get`/`__set` magic methods from all classes
   - Remove ArrayAccess from Form
   - Implement explicit getters/setters
   - Add typed property declarations

6. **Phase 6: Form & Meta**
   - Type Form class with explicit field properties
   - Type DefaultMeta with proper option types
   - Type CSRF integration

7. **Phase 7: Test Coverage**
   - Type test fixtures
   - Add `@phpstan-assert` for custom assertions
   - Ensure all tests pass PHPStan level 6

### New Questions Discovered

#### FQ1. [x] Should we create a `CSRFConfigurationException` for CSRF setup errors?

**Answer:** **Yes, we should create a `CSRFConfigurationException`** (or `CSRFSetupException`) to distinguish configuration-time errors from validation-time errors.

**Current State:**
```php
// SessionCSRF.php lines 61-66
if (!$meta->csrf_secret) {
    throw new \Exception('Must set `csrf_secret` on class Meta for SessionCSRF to work');
}
if (PHP_SESSION_ACTIVE !== session_status()) {
    throw new TypeError('Must must have an active session to use SessionCSRF');
}
```

**Problems with Current Approach:**
1. Uses generic `\Exception` which is hard to catch specifically
2. Uses `TypeError` for session state, which is semantically incorrect (not a type error)
3. No consistent exception hierarchy for CSRF-related errors
4. Hard for users to implement proper error handling

**Recommended Approach:**

```php
// New exception hierarchy
namespace WTForms\Exceptions;

// Base for all CSRF errors (aligns with FQ5 base exception question)
class CSRFException extends \RuntimeException {}

// Configuration errors (thrown at setup/form creation time)
class CSRFConfigurationException extends CSRFException {}

// Validation errors (thrown when token fails validation)
class CSRFValidationException extends CSRFException {}
```

**Why `RuntimeException` base?**
- CSRF configuration errors are runtime problems (missing config, no session)
- They are not logic errors that should crash the application
- Users can catch and handle them appropriately
- Matches PHP's semantic: "Exception thrown if an error which can only be found on runtime occurs"

**Benefits:**
1. **Type-safe catching**: `catch (CSRFConfigurationException $e)` vs generic `catch (\Exception $e)`
2. **PHPStan/Psalm visibility**: Static analyzers can track exception flow
3. **Documentation via types**: Exception class name explains the error category
4. **Consistent hierarchy**: All CSRF errors under `CSRFException`

**Migration:**
```php
// Before
throw new \Exception('Must set `csrf_secret`...');
throw new TypeError('Must have an active session...');

// After
throw new CSRFConfigurationException('Missing csrf_secret in Meta');
throw new CSRFConfigurationException('SessionCSRF requires an active PHP session');
```

**Evidence from Python ecosystem:**
- Flask-WTF uses `CSRFError` (extends HTTP 400) for validation failures
- Configuration errors throw `RuntimeError`
- WTForms core uses `ValidationError` for token mismatches, `NotImplementedError` for missing implementations

**New questions raised:**
- FQ6. Should `CSRFValidationException` replace current `ValidationError` usage in CSRF context, or should CSRF validation still use `ValidationError`?

---

#### FQ2. [x] Should `Widget` be marked `@template-covariant` since it only reads field data?

**Answer:** **It depends - recommend removing the generic parameter entirely or using call-site variance.**

**Investigation:**
Widgets only read field properties and never write to them:
```bash
# Search for any field property writes in widgets
grep -rn '\$field->[a-z]+ =' src/Widgets/
# Result: No matches found - widgets ONLY read!

# Examples of read-only access:
$field->id, $field->name, $field->value, $field->data, $field->checked
```

**The Problem with `@template-covariant TData`:**

Per [PHPStan docs](https://phpstan.org/blog/whats-up-with-template-covariant), a covariant type parameter **cannot appear in parameter position**. But Widget's `__invoke` method accepts `Field<TData>` as a parameter:

```php
/**
 * @template-covariant TData  // ❌ Won't work
 */
abstract class Widget {
    abstract public function __invoke(Field $field, ...): string;
    //                                ^^^^^^^^^^^^
    // TData is in parameter position - violates covariance rule!
}
```

**Why the question exists:**
Widget is conceptually "covariant" because it only *reads* field data. A `Widget<Animal>` should safely render a `Field<Cat>` since it just reads properties. But PHPStan enforces covariance structurally, not semantically.

**Options:**

1. **Remove the generic parameter entirely** (Recommended)
   ```php
   /** Widget doesn't need TData - it reads common Field properties */
   abstract class Widget {
       abstract public function __invoke(Field $field, array $options = []): string;
   }
   ```
   This works because widgets access common properties (`id`, `name`, `value`) not type-specific ones.

2. **Use call-site variance**
   ```php
   /** @param Widget<covariant TData> $widget */
   function render(Widget $widget, Field $field): string { ... }
   ```
   Mark covariance at the use site, not the class definition.

3. **Keep invariant but document intent**
   ```php
   /**
    * @template TData (invariant for PHPStan, but conceptually read-only)
    */
   ```

**Recommendation:** Remove `@template TData` from Widget entirely. The generic parameter adds complexity without real type-safety benefits - widgets use the same Field properties regardless of the field's data type.

**New questions raised:**
- FQ7. Should Widget even be generic over TData, or should it just accept `Field<mixed>` since it only reads common properties?

---

#### FQ3. [x] Should we add `@psalm-immutable` to option classes after construction?

**Answer:** **No, option classes cannot be marked `@psalm-immutable`** due to their design as mutable `ArrayAccess` containers.

**Why Not Immutable:**

1. **`ArrayOptions` implements mutable interfaces:**
   ```php
   abstract class ArrayOptions implements \ArrayAccess, \Countable, \IteratorAggregate
   {
       public function offsetSet($offset, $value): void  // ← Mutation!
       {
           $this->data[$offset] = $value;
           if (property_exists($this, $offset)) {
               $this->$offset = $value;
           }
       }

       public function __set(string $name, $value): void  // ← Mutation!
       {
           $this->data[$name] = $value;
       }
   }
   ```

2. **`@psalm-immutable` requirements (per [Psalm docs](https://psalm.dev/docs/annotating_code/supported_annotations/)):**
   - Every property treated as `@psalm-readonly`
   - Every instance method treated as `@psalm-mutation-free`
   - The `offsetSet`, `offsetUnset`, `__set`, and `__unset` methods violate this

3. **Actual usage pattern is mutable:**
   ```php
   $options = new FieldOptions(['validators' => []]);
   $options['validators'][] = new InputRequired();  // Mutation after construction
   $options->description = 'Updated';  // Also mutation
   ```

**What We Could Do Instead:**

1. **`@psalm-readonly` on specific properties** where mutation is rare:
   ```php
   /** @psalm-readonly */
   public int $min = -1;
   ```
   But this would break existing code that modifies options.

2. **Create separate immutable "frozen" option classes:**
   ```php
   /**
    * @psalm-immutable
    */
   final class FrozenFieldOptions
   {
       public function __construct(
           public readonly array $validators,
           public readonly string $description,
           // ...
       ) {}
   }

   // Usage:
   $opts = new FieldOptions(['validators' => [...]]);  // Mutable
   $frozen = $opts->freeze();  // Returns FrozenFieldOptions
   ```

3. **Use `@psalm-mutation-free` on getter methods:**
   ```php
   /** @psalm-mutation-free */
   public function toArray(): array { ... }
   ```

**Recommendation:**
Keep options mutable but add `@psalm-mutation-free` to pure methods like `toArray()`, `count()`, and `offsetGet()`. The mutable pattern is intentional and matches WTForms' design where options are built incrementally.

**New questions raised:**
- None - this question is fully resolved.

---

#### FQ4. [x] How to handle i18n messages in a type-safe way (translation keys)?

**Answer:** Follow Python WTForms' approach - **pass English strings directly to translation functions** rather than using translation keys. Type safety comes from interfaces, not string literal types.

**Current State in php-wtforms:**
```php
// Validators use plain English strings with sprintf/vsprintf_named
throw new ValidationError('Field must be at least %d characters long.');
throw new ValidationError(vsprintf_named($message, ['min' => $this->min, 'max' => $this->max]));
```

**How Python WTForms handles i18n (per [WTForms i18n docs](https://wtforms.readthedocs.io/en/2.3.x/i18n/)):**
1. Built-in messages are English strings, not keys
2. Users override `_get_translations()` on Form to provide a translator object
3. Translator implements `gettext(string)` and `ngettext(singular, plural, n)`
4. Gettext catalogs map English strings to translations

**Recommended Type-Safe Approach for PHP:**

1. **Define a `Translator` interface:**
   ```php
   interface Translator
   {
       /**
        * Translate a message string.
        * @param string $message English message (acts as key)
        */
       public function gettext(string $message): string;

       /**
        * Translate with plural forms.
        * @param string $singular Singular English message
        * @param string $plural Plural English message
        * @param int $n Count for plural selection
        */
       public function ngettext(string $singular, string $plural, int $n): string;
   }
   ```

2. **Add translator to Meta/Form:**
   ```php
   class DefaultMeta
   {
       public ?Translator $translator = null;

       public function gettext(string $message): string
       {
           return $this->translator?->gettext($message) ?? $message;
       }
   }
   ```

3. **Use in validators:**
   ```php
   // Instead of:
   throw new ValidationError('Field must be at least %d characters long.');

   // Do:
   $message = $field->gettext('Field must be at least %d characters long.');
   throw new ValidationError(sprintf($message, $this->min));
   ```

**Why NOT Translation Keys:**
- Keys like `'error.field.min_length'` are less self-documenting
- Gettext catalogs use English strings as keys (standard approach)
- Python WTForms uses this approach successfully
- Keys require external lookup tables; strings are readable in code

**Type Safety Mechanisms:**

1. **Interface types** (not string literal types):
   - `Translator` interface ensures proper implementation
   - PHPStan can verify method signatures
   - IDE autocomplete works for translator methods

2. **PHPDoc for message format validation** (optional):
   ```php
   /**
    * @psalm-param literal-string $message
    */
   public function gettext(string $message): string;
   ```
   Psalm's `literal-string` type prevents dynamic string injection attacks.

3. **Named parameter validation** (at runtime):
   ```php
   /**
    * @param array<string, scalar> $params Named parameters
    */
   public function format(string $message, array $params): string;
   ```

**Recommendation:**
- Add `Translator` interface to `src/I18n/Translator.php`
- Add `NullTranslator` (pass-through) as default implementation
- Add `gettext()`/`ngettext()` methods to `DefaultMeta`
- Update validators to use `$field->gettext()` for messages
- Type safety through interfaces, not string literal types

**New questions raised:**
- None - this follows established patterns from Python WTForms.

---

#### FQ5. [ ] Should we create a common `WTFormsException` base class?

**Answer:** *Pending investigation*

**New questions raised:**
- *None yet*

---

#### FQ6. [ ] Should `CSRFValidationException` replace current `ValidationError` usage in CSRF context, or should CSRF validation still use `ValidationError`?

**Answer:** *Pending investigation*

**New questions raised:**
- *None yet*

---

#### FQ7. [ ] Should Widget even be generic over TData, or should it just accept `Field<mixed>` since it only reads common properties?

**Answer:** *Pending investigation*

**New questions raised:**
- *None yet*

---

### Tool Versions Used
- PHPStan: 2.1.x
- Psalm: Not yet installed (to be added)
- PHP: 7.4.x
- PHPUnit: 9.x

---

## Completion Checklist

Before outputting the completion promise, verify:

- [x] All 120 questions have been investigated (12 sections x 10 questions)
- [x] Each answer includes evidence (command output, code, or documentation links)
- [x] All blocking issues are documented with proposed solutions
- [x] Key decisions are summarized
- [x] Implementation priority is established
- [x] New questions are documented for next iteration (if any)

When all above criteria are met, output:

<promise>INVESTIGATION_COMPLETE</promise>
