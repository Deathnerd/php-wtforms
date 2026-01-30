---
work_package_id: "WP07"
subtasks:
  - "T031"
  - "T032"
  - "T033"
  - "T034"
  - "T035"
  - "T036"
title: "FormOptions and Deprecation"
phase: "Phase 6+7 - Form Options and Deprecation"
lane: "planned"
assignee: ""
agent: ""
shell_pid: ""
review_status: ""
reviewed_by: ""
dependencies: ["WP02", "WP03", "WP04", "WP05", "WP06"]
history:
  - timestamp: "2026-01-30T00:00:00Z"
    lane: "planned"
    agent: "system"
    shell_pid: ""
    action: "Prompt generated via /spec-kitty.tasks"
---

# Work Package Prompt: WP07 – FormOptions and Deprecation

## ⚠️ IMPORTANT: Review Feedback Status

- **Has review feedback?**: Check the `review_status` field above.
- **You must address all feedback** before your work is complete.

---

## Review Feedback

*[This section is empty initially.]*

---

## Objectives & Success Criteria

- `FormOptions` created in `src/Options/`.
- `Form` has `constructFromOptions` and accepts `array|FormOptions`.
- All array-path branches annotated `@deprecated` across Fields, Validators, Widgets, Form.
- Full test suite passes. PHPStan produces no new errors.

**Implementation command**: `spec-kitty implement WP07 --base WP06`

## Context & Constraints

- **Data model**: `FormOptions` properties: meta, prefix, formdata, obj, data.
- **FR-007**: Existing test files MUST NOT be changed.
- **SC-005**: All array-path branches annotated `@deprecated`.
- **SC-003**: PHPStan must produce no new errors.
- `Form` does not extend `Field` — needs its own `constructFromOptions`.

## Subtasks & Detailed Guidance

### Subtask T031 – Create `FormOptions` and `Form::constructFromOptions`

- **Purpose**: Complete the migration by typing Form's constructor options.
- **Steps**:
  1. Create `src/Options/FormOptions.php`:
     - Extends `ArrayOptions`
     - Properties: `meta` (array|DefaultMeta), `prefix` (string, default ''), `formdata` (array, default []), `obj` (object|null), `data` (array|null)
     - `validate()`: minimal — check types if needed
     - `hydrateObject()`: assigns all properties. Normalizes prefix by appending '-' if non-empty.
  2. Add `Form::constructFromOptions(FormOptions $options): void`:
     - Calls `$options->validate()`
     - Calls `$options->hydrateObject($this)`
     - Calls `$this->postHydrate()` (add no-op hook if not present)
  3. Ensure new `src/Options/` directory is covered by PSR-4 autoloading
- **Files**: `src/Options/FormOptions.php` (NEW), `src/Form.php`
- **Parallel?**: No

### Subtask T032 – Update Form Constructor

- **Purpose**: Accept typed Options.
- **Steps**:
  1. Update `Form::__construct()` to accept `array|FormOptions`
  2. Route: if `FormOptions`, call `constructFromOptions()`. If array, use existing path.
  3. Ensure array path also calls `postHydrate()` for convergence
- **Files**: `src/Form.php`
- **Parallel?**: No — after T031

### Subtask T033 – Deprecate Array Paths in Field Constructors

- **Purpose**: Mark all array-based construction as deprecated.
- **Steps**:
  1. For each Field class with updated constructor (Field, FieldList, SelectField, DateTimeField, FormField, and all leaf fields):
     - Add `@deprecated Use XOptions parameter instead` PHPDoc to the array-path branch
     - This is typically an `if (is_array($options))` branch
  2. Do NOT remove the array path — only annotate it
- **Files**: All field source files in `src/Fields/`
- **Parallel?**: Yes

### Subtask T034 – Deprecate Array Paths in Validator Constructors

- **Purpose**: Mark all validator array construction as deprecated.
- **Steps**:
  1. For each of the 9 validators + Validator base:
     - Add `@deprecated` to array-path branch
- **Files**: All validator source files in `src/Validators/`
- **Parallel?**: Yes

### Subtask T035 – Deprecate Array Paths in Widget and Form Constructors

- **Purpose**: Complete deprecation across all remaining classes.
- **Steps**:
  1. For each widget with updated constructor (Input, PasswordInput, ListWidget, TableWidget, NumberInput, RangeInput):
     - Add `@deprecated` to array/positional-param path
  2. For `Form::__construct()`:
     - Add `@deprecated` to array-path branch
  3. For `CSRFTokenField::__construct()`:
     - Add `@deprecated` to array-path branch
- **Files**: Widget source files in `src/Widgets/`, `src/Form.php`, `src/CSRF/Core/CSRFTokenField.php`
- **Parallel?**: Yes

### Subtask T036 – Run Full Test Suite + PHPStan

- **Purpose**: Final validation of the complete migration.
- **Steps**:
  1. Run `vendor/bin/phpunit` — zero failures
  2. Run `vendor/bin/phpstan analyse --memory-limit=512M` — no new errors
  3. Verify `@deprecated` annotations don't trigger PHPStan warnings at current level
  4. Compare test count with WP01 baseline — must be identical (no tests added or removed)
- **Files**: None (test + analysis execution)
- **Parallel?**: No

## Risks & Mitigations

- **PHPStan deprecation warnings**: `@deprecated` annotations may cause PHPStan to report usages as deprecated. Check if PHPStan config suppresses internal deprecation usage.
- **Form constructor complexity**: `Form::__construct` may be more complex than fields due to meta processing. Read carefully.

## Review Guidance

- Verify `FormOptions::hydrateObject()` normalizes prefix correctly (append '-' if non-empty).
- Verify every migrated class has `@deprecated` on its array path.
- Verify PHPStan is clean.
- Run the full test suite as final gate.

## Activity Log

- 2026-01-30T00:00:00Z – system – lane=planned – Prompt created.
