---
work_package_id: WP02
title: Wire Existing Field Options — Tier 1
lane: "doing"
dependencies: [WP01]
base_branch: 001-typed-options-migration-WP01
base_commit: 2c54e440a640161d115be65fde1a3a73cf4ea895
created_at: '2026-01-30T22:43:20.285289+00:00'
subtasks:
- T005
- T006
- T007
- T008
- T009
phase: Phase 2 - Wire Existing Field Options
assignee: ''
agent: ''
shell_pid: "19848"
review_status: ''
reviewed_by: ''
history:
- timestamp: '2026-01-30T00:00:00Z'
  lane: planned
  agent: system
  shell_pid: ''
  action: Prompt generated via /spec-kitty.tasks
---

# Work Package Prompt: WP02 – Wire Existing Field Options — Tier 1

## ⚠️ IMPORTANT: Review Feedback Status

- **Has review feedback?**: Check the `review_status` field above.
- **You must address all feedback** before your work is complete.

---

## Review Feedback

*[This section is empty initially.]*

---

## Objectives & Success Criteria

- `FieldOptions::hydrateObject()` implemented with pure property assignment.
- `SelectFieldOptions::hydrateObject()` implemented; `SelectField::postHydrate()` sets default Select widget and identity coerce.
- `DateTimeFieldOptions::hydrateObject()` implemented; `DateTimeField::postHydrate()` handles strftime-to-Carbon conversion and default TextInput.
- `SelectField` and `DateTimeField` constructors accept `array|XOptions` union type.
- All existing tests pass without modification.

**Implementation command**: `spec-kitty implement WP02 --base WP01`

## Context & Constraints

- **Spec**: `kitty-specs/001-typed-options-migration/spec.md`
- **Plan**: `kitty-specs/001-typed-options-migration/plan.md`
- **Data model**: `kitty-specs/001-typed-options-migration/data-model.md`
- **FR-007**: Existing test files MUST NOT be changed under any circumstances.
- **Lifecycle**: `validate()` → `hydrateObject()` → `postHydrate()`. `hydrateObject()` is pure assignment only. Derived state goes in `postHydrate()`.
- **Edge case**: Both array path and Options path must converge on `postHydrate()`.

## Subtasks & Detailed Guidance

### Subtask T005 – Implement `FieldOptions::hydrateObject()`

- **Purpose**: This is the foundation — all field Options classes inherit from `FieldOptions`. Its `hydrateObject()` assigns the common field properties.
- **Steps**:
  1. Read `src/Fields/Options/FieldOptions.php` to understand its properties
  2. Implement `hydrateObject(object $obj): object` that assigns:
     - `$obj->label` from Options label (if set)
     - `$obj->description` from Options description
     - `$obj->validators` from Options validators array
     - `$obj->filters` from Options filters array
     - `$obj->id` from Options id
     - `$obj->default` from Options default value
     - `$obj->render_kw` from Options render_kw array
     - Any other properties defined in `FieldOptions`
  3. Return `$obj` after assignment
  4. Do NOT set widgets, compute labels, or do any derived work — that's `postHydrate()`'s job
- **Files**: `src/Fields/Options/FieldOptions.php`
- **Parallel?**: No — must complete before T006/T007
- **Notes**: Use PHPStan `@param` typing on the `$obj` parameter to specify `Field` type for static analysis.

### Subtask T006 – Implement `SelectFieldOptions` + `SelectField::postHydrate()`

- **Purpose**: Wire typed options for SelectField with choices and coerce function.
- **Steps**:
  1. Read `src/Fields/Options/SelectFieldOptions.php` for its properties (choices, coerce)
  2. Implement `hydrateObject()`: assign choices and coerce to the target
  3. Call `parent::hydrateObject($obj)` first to get base field properties
  4. Read `src/Fields/Core/SelectField.php` constructor to understand current widget/coerce setup
  5. Add `SelectField::postHydrate()`:
     - Set default widget to `Select` if not already set
     - Set default coerce to identity function (`strval` or equivalent) if not already set
     - Set default `option_widget` if applicable
  6. Ensure the array path in `SelectField`'s constructor also calls `postHydrate()`
- **Files**: `src/Fields/Options/SelectFieldOptions.php`, `src/Fields/Core/SelectField.php`
- **Parallel?**: Yes (after T005)
- **Notes**: The coerce function is used to convert form data to the correct type. If not provided, it should default to string identity.

### Subtask T007 – Implement `DateTimeFieldOptions` + `DateTimeField::postHydrate()`

- **Purpose**: Wire typed options for DateTimeField with format string.
- **Steps**:
  1. Read `src/Fields/Options/DateTimeFieldOptions.php` for its properties (format)
  2. Implement `hydrateObject()`: assign format to the target
  3. Call `parent::hydrateObject($obj)` first
  4. Read `src/Fields/Core/DateTimeField.php` constructor to understand current format handling
  5. Add `DateTimeField::postHydrate()`:
     - Handle strftime-to-Carbon format conversion if needed
     - Set default widget to `TextInput` if not already set
  6. Ensure array path convergence on `postHydrate()`
- **Files**: `src/Fields/Options/DateTimeFieldOptions.php`, `src/Fields/Core/DateTimeField.php`
- **Parallel?**: Yes (after T005)

### Subtask T008 – Update Field Constructors

- **Purpose**: Make constructors accept the typed Options union type.
- **Steps**:
  1. Update `SelectField::__construct()` signature to accept `array|SelectFieldOptions`
  2. Update `DateTimeField::__construct()` signature to accept `array|DateTimeFieldOptions`
  3. Route: if Options object received, call `constructFromOptions()`. If array, use existing path (which must also call `postHydrate()`).
  4. Add PHPStan type annotations for the union parameter
- **Files**: `src/Fields/Core/SelectField.php`, `src/Fields/Core/DateTimeField.php`
- **Parallel?**: No — after T006 and T007
- **Notes**: The `Field` base class should already handle `array|FieldOptions` routing. Subclasses may need to override `constructFromOptions` to accept their specific Options type.

### Subtask T009 – Run Full Test Suite

- **Purpose**: Verify backward compatibility after all Tier 1 field changes.
- **Steps**:
  1. Run `vendor/bin/phpunit`
  2. Compare test count with WP01 baseline — must be identical
  3. Zero failures required
  4. If failures occur, debug and fix without modifying test files
- **Files**: None (test execution)
- **Parallel?**: No

## Risks & Mitigations

- **`FieldOptions::hydrateObject()` assigns wrong properties**: Will break all fields. Test thoroughly with existing FieldList tests as canary.
- **Array path doesn't call `postHydrate()`**: Existing tests will fail if widget assignment moves to `postHydrate()` but the array path doesn't call it. Must verify convergence first.

## Review Guidance

- Verify `hydrateObject()` does pure assignment only — no derived state.
- Verify `postHydrate()` handles all derived state — no property assignment.
- Verify array path convergence on `postHydrate()`.
- Run existing tests to confirm backward compatibility.

## Activity Log

- 2026-01-30T00:00:00Z – system – lane=planned – Prompt created.
