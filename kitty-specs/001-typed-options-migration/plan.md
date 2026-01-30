# Implementation Plan: Typed Options Migration

**Branch**: `001-typed-options-migration` | **Date**: 2026-01-30 | **Spec**: [spec.md](spec.md)
**Input**: Feature specification from `kitty-specs/001-typed-options-migration/spec.md`

## Summary

Migrate all constructor options from associative arrays to typed `ArrayOptions` subclasses with a three-phase lifecycle (`validate()` → `hydrateObject()` → `postHydrate()`). All five design-doc phases ship as one feature. Existing tests must pass unchanged against the deprecated array path.

## Technical Context

**Language/Version**: PHP 8.5+
**Primary Dependencies**: None (standalone library, Composer package)
**Storage**: N/A
**Testing**: PHPUnit 9+, 80%+ line coverage, all public APIs tested
**Target Platform**: Any PHP 8.5+ runtime
**Project Type**: Single library
**Performance Goals**: N/A — correctness and type safety over micro-optimization
**Constraints**: Backward compatibility with array-based constructors; PHPStan clean at current level
**Scale/Scope**: ~40 classes across Fields, Validators, Widgets, CSRF, Form

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | Notes |
|-----------|--------|-------|
| PHP 8.5+ | ✅ Pass | All new code targets PHP 8.5+ |
| PHPStan level 8 target | ✅ Pass | Typed Options improve static analysis; no new errors expected |
| Type strictness (Java/Kotlin-level) | ✅ Pass | Core motivation of the migration — replacing `mixed` arrays with typed properties |
| PSR-4 autoloading | ✅ Pass | Options classes in sibling `Options/` directories follow PSR-4 |
| PHPUnit 80%+ coverage | ✅ Pass | New Options validation tests; existing tests unchanged |
| No framework dependency | ✅ Pass | No new dependencies |

No violations. No complexity tracking needed.

## Project Structure

### Documentation (this feature)

```
kitty-specs/001-typed-options-migration/
├── plan.md              # This file
├── spec.md              # Feature specification
├── data-model.md        # Options class hierarchy
└── checklists/
    └── requirements.md  # Spec quality checklist
```

### Source Code (repository root)

```
src/
├── Internal/Types/
│   └── ArrayOptions.php          # Base class (exists — validate, abstract hydrateObject)
├── Fields/
│   ├── Core/
│   │   ├── Field.php             # Base (exists — constructFromOptions, postHydrate hook)
│   │   ├── FieldList.php         # hydrateObject done, postHydrate done
│   │   ├── SelectField.php       # Needs hydrateObject, postHydrate
│   │   ├── DateTimeField.php     # Needs hydrateObject, postHydrate
│   │   ├── FormField.php         # Needs new FormFieldOptions + postHydrate
│   │   └── [16 leaf fields]      # Each needs postHydrate override only
│   └── Options/
│       ├── FieldOptions.php      # Exists — needs hydrateObject
│       ├── FieldListOptions.php  # Done
│       ├── SelectFieldOptions.php # Exists — needs hydrateObject
│       ├── DateTimeFieldOptions.php # Exists — needs hydrateObject
│       └── FormFieldOptions.php  # NEW
├── Validators/
│   ├── Validator.php             # Needs constructFromOptions + postHydrate hook
│   └── Options/
│       ├── LengthOptions.php     # Exists — needs validate, hydrateObject
│       ├── NumberRangeOptions.php # Exists — needs hydrateObject
│       ├── AnyOfOptions.php      # Exists — needs validate, hydrateObject
│       ├── OptionalOptions.php   # Exists — needs hydrateObject
│       ├── RegexpOptions.php     # Exists — needs hydrateObject
│       ├── UUIDOptions.php       # Exists — inherits Regexp + default pattern
│       ├── IPAddressOptions.php  # Exists — needs validate, hydrateObject
│       ├── EqualToOptions.php    # Exists — needs validate, hydrateObject
│       └── RequiredIfOptions.php # Exists — needs validate, hydrateObject
├── Widgets/
│   ├── Core/
│   │   └── Widget.php            # Needs constructFromOptions + postHydrate hook
│   └── Options/                  # NEW directory
│       ├── InputOptions.php
│       ├── PasswordInputOptions.php
│       ├── ListWidgetOptions.php
│       ├── TableWidgetOptions.php
│       ├── NumberInputOptions.php
│       └── RangeInputOptions.php
├── CSRF/
│   ├── Core/CSRFTokenField.php   # Needs constructor update
│   └── Options/                  # NEW directory
│       └── CSRFTokenFieldOptions.php
└── Options/                      # NEW directory
    └── FormOptions.php

tests/  # Existing tests unchanged; new tests added alongside
```

**Structure Decision**: Existing PSR-4 layout preserved. New `Options/` directories added as siblings to their target classes. No structural changes to existing code organization.

## Current Migration State

The following is already implemented and needs re-verification, not reimplementation:

| Component | Status | What exists |
|-----------|--------|-------------|
| `ArrayOptions` base | ✅ Exists | `validate()`, abstract `hydrateObject()`, array access |
| `Field::constructFromOptions` | ✅ Exists | Accepts `FieldOptions`, calls `collectFieldFlags` + `postHydrate` |
| `Field::postHydrate` | ✅ Exists | No-op hook for subclasses |
| `FieldListOptions::hydrateObject` | ✅ Exists | Full implementation with validation |

Everything else needs implementation.

## Phase Execution Order

Based on the design doc and dependency analysis:

### Phase 1: Re-verify Base Classes

Re-verify `ArrayOptions`, `Field::constructFromOptions`, `Field::postHydrate`, and `FieldListOptions` are correctly wired. Run existing tests to confirm baseline.

### Phase 2: Wire Existing Field Options (Tier 1)

Add `validate()` and `hydrateObject()` to `FieldOptions`, `SelectFieldOptions`, `DateTimeFieldOptions`. Update `SelectField` and `DateTimeField` constructors to accept typed options. Add `postHydrate()` overrides.

### Phase 3: Wire Existing Validator Options (Tier 1)

Add `constructFromOptions` + `postHydrate` hook to `Validator` base class. Then add `validate()` and `hydrateObject()` to all 9 existing validator Options classes. Update each validator constructor.

### Phase 4: Leaf Field postHydrate (Tier 2)

Add `postHydrate()` override to all 16 leaf fields that reuse `FieldOptions` — each sets its default widget. No new Options classes.

### Phase 5: New Options Classes (Tier 3)

Create `FormFieldOptions`, `CSRFTokenFieldOptions`, and all 6 Widget Options classes. Wire `Widget` base with `constructFromOptions` + `postHydrate` hook. Update constructors.

### Phase 6: Form Options

Create `FormOptions`. Add `constructFromOptions` to `Form` (does not extend `Field`). Update `Form` constructor.

### Phase 7: Deprecation Annotations

Add `@deprecated` to all array-path branches across all migrated classes.

## Risk Assessment

| Risk | Likelihood | Impact | Mitigation |
|------|-----------|--------|------------|
| Existing tests break | Low | High | Run full suite after every file change; array path preserved |
| `hydrateObject` abstract causes fatal on existing Options without impl | High | High | Must implement all `hydrateObject()` methods before tests can run; batch by class family |
| Inheritance chain ordering | Medium | Medium | Process base classes first (Field → FieldList, Validator base → concrete validators) |
| PHPStan regressions | Medium | Medium | Run PHPStan after each phase |

## Key Design Decisions

1. **Both paths converge on `postHydrate()`** — derived state lives in one place, avoiding duplication between array and typed paths.
2. **Base `postHydrate()` is a no-op** — subclasses override without calling parent.
3. **Unknown keys silently ignored** — `ArrayOptions` does not error on extra properties.
4. **No runtime type check in `hydrateObject()`** — PHPStan param typing enforces at analysis time.
5. **No new Options classes for parameterless constructors** — only classes that accept configuration get Options.
