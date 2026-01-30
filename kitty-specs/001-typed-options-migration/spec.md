# Feature Specification: Typed Options Migration

**Feature Branch**: `001-typed-options-migration`
**Created**: 2026-01-30
**Status**: Draft
**Input**: User description: "@docs/plans/2026-01-30-typed-options-migration-design.md"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Library Consumer Constructs Fields with Typed Options (Priority: P1)

A developer using php-wtforms constructs fields, validators, and widgets by passing a typed Options object instead of an associative array. The Options object provides IDE autocompletion, static analysis support, and early validation of constructor parameters.

**Why this priority**: This is the core value of the migration — replacing untyped arrays with discoverable, statically-analyzable option objects across all constructable classes.

**Independent Test**: Can be tested by constructing any field, validator, or widget with a typed Options object and verifying the resulting object has the correct state.

**Acceptance Scenarios**:

1. **Given** a `FieldOptions` object with label and validators set, **When** a `StringField` is constructed with it, **Then** the field has the correct label, validators, and default `TextInput` widget.
2. **Given** a `LengthOptions` object with min=5 and max=10, **When** a `Length` validator is constructed with it, **Then** the validator enforces the correct range.
3. **Given** a `ListWidgetOptions` object with html_tag='ul' and prefix_label=false, **When** a `ListWidget` is constructed with it, **Then** the widget renders with those settings.

---

### User Story 2 - Existing Array-Based Construction Still Works (Priority: P1)

A developer using the existing array-based constructor path continues to have all code work without modification. The array path is marked `@deprecated` but remains fully functional.

**Why this priority**: Backward compatibility is non-negotiable — all existing tests and consumer code must pass unchanged.

**Independent Test**: Run the full existing test suite without modification. Every test must pass.

**Acceptance Scenarios**:

1. **Given** the existing test suite with array-based constructors, **When** tests are run after the migration, **Then** all tests pass without changes.
2. **Given** a field constructed with an associative array, **When** the constructor receives the array, **Then** the field behaves identically to the pre-migration version.

---

### User Story 3 - Options Objects Validate Input at Construction Time (Priority: P2)

When a developer provides invalid options (e.g., `min > max` for Length, missing required `form_class` for FormField), the Options object's `validate()` method throws a `TypeError` or `ValidationError` immediately — before any object state is modified.

**Why this priority**: Early validation catches configuration errors at construction time rather than at form-processing time, improving developer experience.

**Independent Test**: Construct Options objects with invalid parameters and verify the correct exception is thrown.

**Acceptance Scenarios**:

1. **Given** a `LengthOptions` with min=10 and max=5, **When** `validate()` runs, **Then** a `TypeError` or `ValidationError` is thrown.
2. **Given** a `FormFieldOptions` without a `form_class`, **When** `validate()` runs, **Then** a `TypeError` is thrown.
3. **Given** a `FieldListOptions` with filters provided, **When** `validate()` runs, **Then** an appropriate error is thrown.

---

### User Story 4 - Three-Phase Lifecycle Separates Concerns (Priority: P2)

Each constructable class follows the `validate()` → `hydrateObject()` → `postHydrate()` lifecycle. Validation is pure (no side effects), hydration is pure property assignment, and post-hydration handles derived state (default widgets, computed properties).

**Why this priority**: Clean separation makes the construction pipeline predictable and testable at each stage.

**Independent Test**: For a representative class (e.g., FieldList), verify that validate rejects bad input, hydrateObject assigns properties without side effects, and postHydrate sets the default widget.

**Acceptance Scenarios**:

1. **Given** a valid `FieldListOptions`, **When** `hydrateObject()` runs, **Then** only `inner_field`, `min_entries`, and `max_entries` are assigned — no widget is set yet.
2. **Given** a `FieldList` after hydration, **When** `postHydrate()` runs, **Then** the default `ListWidget` is set.

---

### User Story 5 - New Options Classes for Widgets, FormField, CSRF, and Form (Priority: P3)

New Options classes are created for Widget subclasses (`InputOptions`, `PasswordInputOptions`, `ListWidgetOptions`, `TableWidgetOptions`, `NumberInputOptions`, `RangeInputOptions`), `FormFieldOptions`, `CSRFTokenFieldOptions`, and `FormOptions`.

**Why this priority**: These classes complete the migration across all constructable types, but build on the patterns established in P1/P2.

**Independent Test**: Construct each new Options class with valid and invalid parameters, then construct the target object and verify correct state.

**Acceptance Scenarios**:

1. **Given** a `FormFieldOptions` with a valid `form_class` and separator, **When** a `FormField` is constructed, **Then** it has the correct form class, separator, and default `TableWidget`.
2. **Given** a `FormOptions` with meta, prefix, and formdata, **When** a `Form` is constructed, **Then** the form has normalized prefix (with '-' appended) and correct meta.

---

### Edge Cases

- Unknown keys in the source array are silently ignored by `ArrayOptions` — no error thrown, supports forward compatibility.
- Both the array path and the typed Options path converge on `postHydrate()`. Derived state (default widgets, computed properties) lives in `postHydrate()` only, avoiding duplication.
- Base `postHydrate()` on `Field`, `Validator`, and `Widget` is a no-op hook. Subclasses override it without needing to call `parent::postHydrate()`.
- `hydrateObject()` target type is enforced by PHPStan param typing at analysis time. No runtime class check is performed.

## Clarifications

### Session 2026-01-30

- Q: What happens when an Options object has extra/unknown properties? → A: Silently ignore unknown keys.
- Q: Does the array path also call `postHydrate()`? → A: Yes — both paths converge on `postHydrate()` so derived state lives in one place.
- Q: Must subclass `postHydrate()` call `parent::postHydrate()`? → A: No — base is a no-op hook, calling parent is harmless but not required.
- Q: Runtime check when `hydrateObject()` receives wrong target class? → A: No runtime check — rely on PHPStan param typing at analysis time.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST accept typed Options objects as constructor parameters for all Fields, Validators, Widgets, CSRF token fields, and Forms.
- **FR-002**: System MUST continue to accept associative arrays as constructor parameters (backward compatibility). The array path MUST be marked `@deprecated`.
- **FR-003**: Each Options class MUST implement `validate()` that throws `TypeError` or `ValidationError` for invalid input, with no side effects.
- **FR-004**: Each Options class MUST implement `hydrateObject()` that performs pure property assignment from Options to target object.
- **FR-005**: Target classes MUST implement `postHydrate()` for derived state (default widgets, label promotion, computed properties) where applicable.
- **FR-006**: The lifecycle MUST execute in order: `validate()` → `hydrateObject()` → `postHydrate()`.
- **FR-007**: All existing tests MUST pass without modification when using the array-based constructor path.
- **FR-008**: Base classes (`Field`, `Validator`, `Widget`) MUST provide `constructFromOptions` and `postHydrate` hook infrastructure.
- **FR-009**: New Options classes MUST be created for: `FormFieldOptions`, `CSRFTokenFieldOptions`, `InputOptions`, `PasswordInputOptions`, `ListWidgetOptions`, `TableWidgetOptions`, `NumberInputOptions`, `RangeInputOptions`, and `FormOptions`.
- **FR-010**: Options classes MUST reside in a sibling `Options/` directory following the naming convention `{ClassName}Options`.
- **FR-011**: Fields that reuse `FieldOptions` without new properties MUST only add a `postHydrate()` override for their default widget (no new Options class needed).

### Key Entities

- **ArrayOptions**: Base class for all typed options. Provides `validate()` and `hydrateObject()` contract.
- **FieldOptions / FieldListOptions / SelectFieldOptions / DateTimeFieldOptions**: Existing Options classes for field configuration.
- **Validator Options** (Length, NumberRange, AnyOf, Optional, Regexp, UUID, IPAddress, EqualTo, RequiredIf): Typed options for validator construction.
- **Widget Options** (Input, PasswordInput, ListWidget, TableWidget, NumberInput, RangeInput): New options for widget construction.
- **FormFieldOptions / CSRFTokenFieldOptions / FormOptions**: New options for composite and top-level classes.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: All existing tests pass without modification after migration is complete.
- **SC-002**: Every constructable class (Fields, Validators, Widgets, CSRF, Form) accepts a typed Options object as its constructor parameter.
- **SC-003**: PHPStan analysis at the current configured level produces no new errors related to the Options migration.
- **SC-004**: Every Options class with validation rules throws on invalid input (verified by dedicated tests).
- **SC-005**: The array-based constructor path is annotated `@deprecated` across all migrated classes.
