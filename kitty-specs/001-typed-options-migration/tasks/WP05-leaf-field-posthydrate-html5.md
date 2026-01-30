---
work_package_id: "WP05"
subtasks:
  - "T021"
  - "T022"
  - "T023"
title: "Leaf Field postHydrate — HTML5 Fields"
phase: "Phase 4 - Leaf Field postHydrate"
lane: "done"
assignee: ""
agent: ""
shell_pid: ""
review_status: "approved"
reviewed_by: "Wes Gilleland"
dependencies: ["WP02"]
history:
  - timestamp: "2026-01-30T00:00:00Z"
    lane: "planned"
    agent: "system"
    shell_pid: ""
    action: "Prompt generated via /spec-kitty.tasks"
---

# Work Package Prompt: WP05 – Leaf Field postHydrate — HTML5 Fields

## ⚠️ IMPORTANT: Review Feedback Status

- **Has review feedback?**: Check the `review_status` field above.
- **You must address all feedback** before your work is complete.

---

## Review Feedback

*[This section is empty initially.]*

---

## Objectives & Success Criteria

- All HTML5 leaf fields have `postHydrate()` override setting their default widget.
- No new Options classes — all reuse `FieldOptions`.
- All existing tests pass without modification.

**Implementation command**: `spec-kitty implement WP05 --base WP02`

## Context & Constraints

- Same pattern as WP04 but for HTML5 field subclasses in `src/Fields/HTML5/`.
- **FR-007**: Existing test files MUST NOT be changed.
- **Pattern**: `if ($this->widget === null) { $this->widget = new XInput(); }`

## Subtasks & Detailed Guidance

### Subtask T021 – postHydrate for Email, URL, Tel, Search

- **Purpose**: Add default HTML5 input widgets.
- **Steps**:
  1. **EmailField** (`src/Fields/HTML5/EmailField.php`): `postHydrate()` → `EmailInput`
  2. **URLField** (`src/Fields/HTML5/URLField.php`): `postHydrate()` → `URLInput`
  3. **TelField** (`src/Fields/HTML5/TelField.php`): `postHydrate()` → `TelInput`
  4. **SearchField** (`src/Fields/HTML5/SearchField.php`): `postHydrate()` → `SearchInput`
  5. Remove inline widget assignment from each constructor if present
- **Files**: `src/Fields/HTML5/EmailField.php`, `URLField.php`, `TelField.php`, `SearchField.php`
- **Parallel?**: Yes

### Subtask T022 – postHydrate for DateTimeLocal and Range Fields

- **Purpose**: Add default widgets for remaining HTML5 fields.
- **Steps**:
  1. **DateTimeLocalField** (`src/Fields/HTML5/DateTimeLocalField.php`): `postHydrate()` → `DateTimeLocalInput`
  2. **Range fields** (find in `src/Fields/HTML5/`): `postHydrate()` → `RangeInput`
  3. Remove inline widget assignment from each constructor
- **Files**: `src/Fields/HTML5/DateTimeLocalField.php`, range field files
- **Parallel?**: Yes

### Subtask T023 – Run Full Test Suite

- **Purpose**: Verify backward compatibility.
- **Steps**:
  1. Run `vendor/bin/phpunit`
  2. Zero failures
- **Parallel?**: No

## Risks & Mitigations

- Low risk — simple widget assignments on well-isolated classes.

## Review Guidance

- Verify correct widget class per data-model.md.
- Verify HTML5 tests still pass (particularly `tests/Fields/HTML5FieldsTest.php`).

## Activity Log

- 2026-01-30T00:00:00Z – system – lane=planned – Prompt created.
- 2026-01-30T23:19:57Z – unknown – lane=done – All 11 HTML5 leaf fields have postHydrate(). 248 tests, same 8 pre-existing failures.
