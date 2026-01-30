---
work_package_id: WP06
title: New Options Classes — Widgets, FormField, CSRF
lane: planned
dependencies:
- WP01
subtasks:
- T024
- T025
- T026
- T027
- T028
- T029
- T030
phase: Phase 5 - New Options Classes
assignee: ''
agent: ''
shell_pid: ''
review_status: ''
reviewed_by: ''
history:
- timestamp: '2026-01-30T00:00:00Z'
  lane: planned
  agent: system
  shell_pid: ''
  action: Prompt generated via /spec-kitty.tasks
---

# Work Package Prompt: WP06 – New Options Classes — Widgets, FormField, CSRF

## ⚠️ IMPORTANT: Review Feedback Status

- **Has review feedback?**: Check the `review_status` field above.
- **You must address all feedback** before your work is complete.

---

## Review Feedback

*[This section is empty initially.]*

---

## Objectives & Success Criteria

- `Widget` base class has `constructFromOptions` and `postHydrate` hook.
- 6 new Widget Options classes created in `src/Widgets/Options/`.
- `FormFieldOptions` created in `src/Fields/Options/`.
- `CSRFTokenFieldOptions` created in `src/CSRF/Options/`.
- All widget, FormField, and CSRFTokenField constructors accept typed Options.
- All existing tests pass without modification.

**Implementation command**: `spec-kitty implement WP06 --base WP02`

## Context & Constraints

- **Data model**: See `kitty-specs/001-typed-options-migration/data-model.md` for Widget Options properties.
- **FR-007**: Existing test files MUST NOT be changed.
- **FR-010**: Options classes in sibling `Options/` directory with `{ClassName}Options` naming.
- **New directories**: `src/Widgets/Options/`, `src/CSRF/Options/` — ensure PSR-4 autoloading.
- Verify `composer.json` autoload config covers these new directories.

## Subtasks & Detailed Guidance

### Subtask T024 – Add `constructFromOptions` and `postHydrate` to Widget Base

- **Purpose**: Establish lifecycle infrastructure for all widgets.
- **Steps**:
  1. Read `src/Widgets/Core/Widget.php`
  2. Add `constructFromOptions(ArrayOptions $options): void`:
     - Calls `$options->validate()`
     - Calls `$options->hydrateObject($this)`
     - Calls `$this->postHydrate()`
  3. Add `postHydrate(): void` as protected no-op hook
  4. Update widget constructors to detect Options vs array/params and route
- **Files**: `src/Widgets/Core/Widget.php`
- **Parallel?**: No — must complete first
- **Notes**: Widgets may have simpler constructors than Fields. Some take positional params rather than arrays. Adapt the routing pattern accordingly.

### Subtask T025 – Create `InputOptions` and `PasswordInputOptions`

- **Purpose**: Typed options for the base Input widget and PasswordInput.
- **Steps**:
  1. Create `src/Widgets/Options/InputOptions.php`:
     - Extends `ArrayOptions`
     - Property: `type` (string) — the HTML input type
     - `hydrateObject()`: assigns type to target
     - `validate()`: no special validation needed
  2. Create `src/Widgets/Options/PasswordInputOptions.php`:
     - Extends `InputOptions` (or `ArrayOptions`)
     - Property: `hide_value` (bool, default true)
     - `hydrateObject()`: assigns hide_value to target
  3. Update `Input` and `PasswordInput` constructors to accept Options
- **Files**: `src/Widgets/Options/InputOptions.php` (NEW), `src/Widgets/Options/PasswordInputOptions.php` (NEW), `src/Widgets/Core/Input.php`, `src/Widgets/Core/PasswordInput.php` (find actual path)
- **Parallel?**: Yes (after T024)

### Subtask T026 – Create `ListWidgetOptions` and `TableWidgetOptions`

- **Purpose**: Typed options for container widgets.
- **Steps**:
  1. Create `src/Widgets/Options/ListWidgetOptions.php`:
     - Properties: `html_tag` (string, default 'ul'), `prefix_label` (bool, default true)
     - `hydrateObject()`: assigns both properties
  2. Create `src/Widgets/Options/TableWidgetOptions.php`:
     - Property: `with_table_tag` (bool, default true)
     - `hydrateObject()`: assigns with_table_tag
  3. Update `ListWidget` and `TableWidget` constructors
- **Files**: `src/Widgets/Options/ListWidgetOptions.php` (NEW), `src/Widgets/Options/TableWidgetOptions.php` (NEW), widget source files
- **Parallel?**: Yes (after T024)

### Subtask T027 – Create `NumberInputOptions` and `RangeInputOptions`

- **Purpose**: Typed options for numeric HTML5 widgets.
- **Steps**:
  1. Create `src/Widgets/Options/NumberInputOptions.php`:
     - Properties: `step` (string|int|null), `min` (string|int|null), `max` (string|int|null)
     - `hydrateObject()`: assigns step, min, max
  2. Create `src/Widgets/Options/RangeInputOptions.php`:
     - Property: `step` (string|int|null)
     - `hydrateObject()`: assigns step
  3. Update `NumberInput` and `RangeInput` constructors
- **Files**: `src/Widgets/Options/NumberInputOptions.php` (NEW), `src/Widgets/Options/RangeInputOptions.php` (NEW), `src/Widgets/HTML5/NumberInput.php`, `src/Widgets/HTML5/RangeInput.php`
- **Parallel?**: Yes (after T024)

### Subtask T028 – Create `FormFieldOptions` and Wire `FormField`

- **Purpose**: Typed options for the composite FormField.
- **Steps**:
  1. Create `src/Fields/Options/FormFieldOptions.php`:
     - Extends `FieldOptions`
     - Properties: `form_class` (class-string, required), `separator` (string, default '-')
     - `validate()`: TypeError if form_class missing, filters provided, or validators provided
     - `hydrateObject()`: call parent, then assign form_class, separator
  2. Update `FormField::__construct()` to accept `array|FormFieldOptions`
  3. Add `FormField::postHydrate()`: set default `TableWidget` if not set
- **Files**: `src/Fields/Options/FormFieldOptions.php` (NEW), `src/Fields/Core/FormField.php`
- **Parallel?**: No — independent of widget work but depends on WP02

### Subtask T029 – Create `CSRFTokenFieldOptions` and Wire `CSRFTokenField`

- **Purpose**: Typed options for CSRF token field.
- **Steps**:
  1. Create `src/CSRF/Options/CSRFTokenFieldOptions.php`:
     - Properties: `csrf_impl` (class-string|CSRF instance, required)
     - `validate()`: csrf_impl must be present and valid (class exists or is CSRF instance)
     - `hydrateObject()`: assigns csrf_impl, instantiates if class-string
  2. Update `CSRFTokenField::__construct()` to accept Options
  3. CSRFTokenField inherits HiddenField defaults via `postHydrate()` chain
- **Files**: `src/CSRF/Options/CSRFTokenFieldOptions.php` (NEW), `src/CSRF/Core/CSRFTokenField.php`
- **Parallel?**: No — independent

### Subtask T030 – Run Full Test Suite

- **Purpose**: Verify backward compatibility with all new Options classes.
- **Steps**:
  1. Run `vendor/bin/phpunit`
  2. Zero failures
  3. Verify PSR-4 autoloading works for new directories
- **Parallel?**: No

## Risks & Mitigations

- **PSR-4 autoloading**: New directories `src/Widgets/Options/` and `src/CSRF/Options/` must be covered by the existing `WTForms\` namespace mapping. Check `composer.json`.
- **Widget constructor patterns differ from Fields**: Widgets may use positional params. Adapt Options routing pattern.

## Review Guidance

- Verify all new files follow `{ClassName}Options` naming in sibling `Options/` directory.
- Verify `FormFieldOptions::validate()` rejects filters and validators.
- Verify CSRFTokenField inherits HiddenField defaults.

## Activity Log

- 2026-01-30T00:00:00Z – system – lane=planned – Prompt created.
