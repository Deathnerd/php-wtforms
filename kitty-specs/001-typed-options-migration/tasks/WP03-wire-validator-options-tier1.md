---
work_package_id: "WP03"
subtasks:
  - "T010"
  - "T011"
  - "T012"
  - "T013"
  - "T014"
  - "T015"
  - "T016"
title: "Wire Validator Base and Options — Tier 1"
phase: "Phase 3 - Wire Existing Validator Options"
lane: "planned"
assignee: ""
agent: ""
shell_pid: ""
review_status: ""
reviewed_by: ""
dependencies: ["WP01"]
history:
  - timestamp: "2026-01-30T00:00:00Z"
    lane: "planned"
    agent: "system"
    shell_pid: ""
    action: "Prompt generated via /spec-kitty.tasks"
---

# Work Package Prompt: WP03 – Wire Validator Base and Options — Tier 1

## ⚠️ IMPORTANT: Review Feedback Status

- **Has review feedback?**: Check the `review_status` field above.
- **You must address all feedback** before your work is complete.

---

## Review Feedback

*[This section is empty initially.]*

---

## Objectives & Success Criteria

- `Validator` base class has `constructFromOptions` and `postHydrate` hook (mirroring `Field`).
- All 9 existing validator Options classes have `validate()` and `hydrateObject()` implemented.
- All 9 validator constructors accept `array|XOptions` union type.
- All existing tests pass without modification.

**Implementation command**: `spec-kitty implement WP03 --base WP01`

## Context & Constraints

- **Data model**: See `kitty-specs/001-typed-options-migration/data-model.md` for validator Options properties and validation rules.
- **FR-007**: Existing test files MUST NOT be changed.
- **Risk**: Abstract `hydrateObject()` means ALL 9 Options classes must be implemented in this WP, or autoloading any of them will cause fatals.
- **Pattern**: Mirror `Field::constructFromOptions` approach in `Validator`.

## Subtasks & Detailed Guidance

### Subtask T010 – Add `constructFromOptions` and `postHydrate` to Validator Base

- **Purpose**: Establish the lifecycle infrastructure for all validators.
- **Steps**:
  1. Read `src/Validators/Validator.php` to understand current constructor
  2. Add `constructFromOptions(ArrayOptions $options): void` method:
     - Calls `$options->validate()`
     - Calls `$options->hydrateObject($this)`
     - Calls `$this->postHydrate()`
  3. Add `postHydrate(): void` as protected no-op hook
  4. Update constructor to detect Options object vs array and route accordingly
  5. Ensure the array path also calls `postHydrate()` for convergence
- **Files**: `src/Validators/Validator.php`
- **Parallel?**: No — must complete first
- **Notes**: The param type for `constructFromOptions` should be broad enough for all validator Options types. Consider using the common base `ArrayOptions` or a validator-specific base if one exists.

### Subtask T011 – `LengthOptions` and `NumberRangeOptions`

- **Purpose**: Implement the two range-based validator Options.
- **Steps**:
  1. **LengthOptions** (`src/Validators/Options/LengthOptions.php`):
     - `validate()`: At least one of min/max must be present. If both present, min <= max. Throw `TypeError` on violation.
     - `hydrateObject()`: Assign min, max to target. Call parent if applicable.
  2. **NumberRangeOptions** (`src/Validators/Options/NumberRangeOptions.php`):
     - `validate()`: No validation needed (both min and max are optional).
     - `hydrateObject()`: Assign min, max to target.
  3. Update `Length` and `NumberRange` constructors in T015.
- **Files**: `src/Validators/Options/LengthOptions.php`, `src/Validators/Options/NumberRangeOptions.php`
- **Parallel?**: No — implement early as reference pattern

### Subtask T012 – `AnyOfOptions` and `OptionalOptions`

- **Purpose**: Implement collection and whitespace validator Options.
- **Steps**:
  1. **AnyOfOptions** (`src/Validators/Options/AnyOfOptions.php`):
     - `validate()`: values must be array, formatter must be callable (if provided)
     - `hydrateObject()`: Assign values, formatter to target
  2. **OptionalOptions** (`src/Validators/Options/OptionalOptions.php`):
     - `validate()`: No validation needed
     - `hydrateObject()`: Assign strip_whitespace to target
- **Files**: `src/Validators/Options/AnyOfOptions.php`, `src/Validators/Options/OptionalOptions.php`
- **Parallel?**: Yes (after T010)

### Subtask T013 – `RegexpOptions`, `UUIDOptions`, `IPAddressOptions`

- **Purpose**: Implement pattern-based validator Options.
- **Steps**:
  1. **RegexpOptions** (`src/Validators/Options/RegexpOptions.php`):
     - `validate()`: Verify regex is a valid regular expression (use `@preg_match` test)
     - `hydrateObject()`: Assign regex to target
  2. **UUIDOptions** (`src/Validators/Options/UUIDOptions.php`):
     - Extends `RegexpOptions`. Override to provide default UUID regex pattern.
     - `hydrateObject()`: Call parent, override regex with UUID pattern if not set
  3. **IPAddressOptions** (`src/Validators/Options/IPAddressOptions.php`):
     - `validate()`: Verify ip_type is a valid `FILTER_FLAG_IPV*` flag value
     - `hydrateObject()`: Assign ip_type to target
- **Files**: `src/Validators/Options/RegexpOptions.php`, `src/Validators/Options/UUIDOptions.php`, `src/Validators/Options/IPAddressOptions.php`
- **Parallel?**: Yes (after T010)

### Subtask T014 – `EqualToOptions` and `RequiredIfOptions`

- **Purpose**: Implement field-reference validator Options.
- **Steps**:
  1. **EqualToOptions** (`src/Validators/Options/EqualToOptions.php`):
     - `validate()`: fieldname must be present and non-empty string
     - `hydrateObject()`: Assign fieldname to target
  2. **RequiredIfOptions** (`src/Validators/Options/RequiredIfOptions.php`):
     - `validate()`: fieldname must be present and non-empty string
     - `hydrateObject()`: Assign fieldname to target
- **Files**: `src/Validators/Options/EqualToOptions.php`, `src/Validators/Options/RequiredIfOptions.php`
- **Parallel?**: Yes (after T010)

### Subtask T015 – Update All 9 Validator Constructors

- **Purpose**: Make every validator accept typed Options.
- **Steps**:
  1. For each of the 9 validators (Length, NumberRange, AnyOf, Optional, Regexp, UUID, IPAddress, EqualTo, RequiredIf):
     - Update constructor signature to accept `array|XOptions` union
     - Route: if Options received, call `constructFromOptions()`. If array, use existing path.
  2. Ensure the array path in each validator also calls `postHydrate()` for convergence
- **Files**: All 9 validator source files in `src/Validators/`
- **Parallel?**: No — must run after T011–T014

### Subtask T016 – Run Full Test Suite

- **Purpose**: Verify backward compatibility.
- **Steps**:
  1. Run `vendor/bin/phpunit`
  2. Zero failures required
  3. If failures, debug without modifying test files
- **Parallel?**: No

## Risks & Mitigations

- **All-or-nothing**: All 9 `hydrateObject()` implementations must land together (abstract base). Implement in one WP.
- **Validator constructor varies**: Each validator may have a different constructor pattern. Read each carefully.

## Review Guidance

- Verify each `validate()` throws on the correct conditions per data-model.md.
- Verify `hydrateObject()` is pure assignment.
- Verify all 9 constructors route correctly.

## Activity Log

- 2026-01-30T00:00:00Z – system – lane=planned – Prompt created.
