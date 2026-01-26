# Investigation Answers
## PHP-WTForms Comprehensive Typing Initiative

**Investigation Started:** 2026-01-26
**Last Updated:** 2026-01-26
**Current Section:** 2
**Status:** IN_PROGRESS

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
