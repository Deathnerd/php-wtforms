---
work_package_id: "WP01"
subtasks:
  - "T001"
  - "T002"
  - "T003"
  - "T004"
title: "Re-verify Base Class Infrastructure"
phase: "Phase 1 - Re-verify Base Classes"
lane: "doing"
assignee: ""
agent: "claude-code"
shell_pid: "1732"
review_status: ""
reviewed_by: ""
dependencies: []
history:
  - timestamp: "2026-01-30T00:00:00Z"
    lane: "planned"
    agent: "system"
    shell_pid: ""
    action: "Prompt generated via /spec-kitty.tasks"
---

# Work Package Prompt: WP01 – Re-verify Base Class Infrastructure

## ⚠️ IMPORTANT: Review Feedback Status

- **Has review feedback?**: Check the `review_status` field above.
- **You must address all feedback** before your work is complete.

---

## Review Feedback

*[This section is empty initially.]*

---

## Objectives & Success Criteria

- Verify that `ArrayOptions`, `Field::constructFromOptions`, `Field::postHydrate`, `Field::collectFieldFlags`, and `FieldListOptions::hydrateObject` are correctly implemented.
- Confirm the three-phase lifecycle (`validate()` → `hydrateObject()` → `postHydrate()`) works end-to-end for `FieldList`.
- Full PHPUnit test suite passes with zero failures.
- Document any issues found for follow-up in subsequent WPs.

**Implementation command**: `spec-kitty implement WP01`

## Context & Constraints

- **Spec**: `kitty-specs/001-typed-options-migration/spec.md`
- **Plan**: `kitty-specs/001-typed-options-migration/plan.md`
- **Data model**: `kitty-specs/001-typed-options-migration/data-model.md`
- **Constitution**: `.kittify/memory/constitution.md` (PHP 8.5+, PHPStan level 8 target, strict typing)
- **FR-007**: Existing test files MUST NOT be changed under any circumstances.
- **Design doc**: `docs/plans/2026-01-30-typed-options-migration-design.md`

## Subtasks & Detailed Guidance

### Subtask T001 – Audit `ArrayOptions` Base Class

- **Purpose**: Confirm `ArrayOptions` has the correct abstract contract for the migration.
- **Steps**:
  1. Read `src/Internal/Types/ArrayOptions.php`
  2. Verify `validate()` method exists (public, can be overridden, default is no-op or minimal)
  3. Verify `hydrateObject(object $obj): object` is declared abstract
  4. Verify `from(array|ArrayOptions $options): static` factory method exists
  5. Verify unknown keys are silently ignored (no error on extra properties)
  6. Check that `toArray()`, array access, and iteration work correctly
- **Files**: `src/Internal/Types/ArrayOptions.php`
- **Parallel?**: Yes (read-only audit)
- **Notes**: If `hydrateObject` is abstract, all concrete Options subclasses must implement it or tests will fatal. This is the critical risk to identify early.

### Subtask T002 – Audit `Field` Base Class Wiring

- **Purpose**: Confirm `Field::constructFromOptions` correctly orchestrates the lifecycle.
- **Steps**:
  1. Read `src/Fields/Core/Field.php`
  2. Verify `constructFromOptions(FieldOptions $options)` exists and:
     - Calls `$options->validate()`
     - Calls `$options->hydrateObject($this)` (or equivalent)
     - Calls `$this->collectFieldFlags()`
     - Calls `$this->postHydrate()`
  3. Verify `postHydrate(): void` exists as a protected no-op hook
  4. Verify `collectFieldFlags(): void` exists and processes validator flags
  5. Verify the constructor accepts `array|FieldOptions` and routes to the correct path
- **Files**: `src/Fields/Core/Field.php`
- **Parallel?**: Yes (read-only audit)
- **Notes**: The array path must also call `postHydrate()` — verify this convergence.

### Subtask T003 – Audit `FieldListOptions` Implementation

- **Purpose**: Confirm the one complete Tier 1 implementation works end-to-end.
- **Steps**:
  1. Read `src/Fields/Options/FieldListOptions.php`
  2. Verify `validate()` checks: inner_field required, no filters allowed
  3. Verify `hydrateObject()` assigns inner_field, min_entries, max_entries
  4. Read `src/Fields/Core/FieldList.php`
  5. Verify `FieldList::postHydrate()` sets default `ListWidget`
  6. Manually trace a `new FieldList(new FieldListOptions([...]))` call through the lifecycle
- **Files**: `src/Fields/Options/FieldListOptions.php`, `src/Fields/Core/FieldList.php`
- **Parallel?**: Yes (read-only audit)

### Subtask T004 – Run Baseline Test Suite

- **Purpose**: Establish green baseline before any code changes.
- **Steps**:
  1. Run `vendor/bin/phpunit` (or Docker equivalent per CLAUDE.md)
  2. Confirm zero failures
  3. Record test count and execution time as baseline
  4. If any tests fail, document the failures — they are pre-existing and must be resolved before proceeding
- **Files**: None (test execution only)
- **Parallel?**: No (must run after audits to contextualize any failures)

## Risks & Mitigations

- **Abstract `hydrateObject` fatals**: If concrete Options classes don't implement it, autoloading them will cause fatal errors. The audit must identify which classes need implementation before any code runs.
- **Array path not calling `postHydrate()`**: If the legacy array path doesn't converge on `postHydrate()`, moving widget assignment there will break backward compatibility.

## Review Guidance

- Reviewer should verify the audit findings are accurate.
- If issues are found, they should be documented as follow-up items with clear WP assignments.
- The baseline test count should be recorded for comparison after each subsequent WP.

## Activity Log

- 2026-01-30T00:00:00Z – system – lane=planned – Prompt created.
- 2026-01-30T22:35:56Z – claude-opus – shell_pid=16784 – lane=doing – Started implementation via workflow command
- 2026-01-30T22:40:10Z – claude-opus – shell_pid=16784 – lane=doing – Audit complete. T001-T003 PASS: ArrayOptions, Field base class, and FieldListOptions all correctly implemented. T004 BLOCKED: FieldOptions missing hydrateObject() implementation causes fatal error - tests cannot run. This is a pre-existing issue that must be resolved by a subsequent WP (likely WP02) adding hydrateObject() to FieldOptions before the test suite can pass.
- 2026-01-30T22:40:27Z – claude-opus – shell_pid=16784 – lane=doing – T004 (baseline tests) cannot be marked done - FieldOptions lacks hydrateObject() causing fatal. Documented as follow-up for WP02.
- 2026-01-30T22:42:18Z – claude-opus – shell_pid=16784 – lane=for_review – Audit complete. T001-T003 pass. T004 documented: FieldOptions missing hydrateObject() blocks tests. See docs/plans/WP01-audit-findings.md.
- 2026-01-30T23:07:14Z – claude-code – shell_pid=1732 – lane=doing – Started review via workflow command
