---
work_package_id: "WP04"
subtasks:
  - "T017"
  - "T018"
  - "T019"
  - "T020"
title: "Leaf Field postHydrate — Core Fields"
phase: "Phase 4 - Leaf Field postHydrate"
lane: "planned"
assignee: ""
agent: ""
shell_pid: ""
review_status: ""
reviewed_by: ""
dependencies: ["WP02"]
history:
  - timestamp: "2026-01-30T00:00:00Z"
    lane: "planned"
    agent: "system"
    shell_pid: ""
    action: "Prompt generated via /spec-kitty.tasks"
---

# Work Package Prompt: WP04 – Leaf Field postHydrate — Core Fields

## ⚠️ IMPORTANT: Review Feedback Status

- **Has review feedback?**: Check the `review_status` field above.
- **You must address all feedback** before your work is complete.

---

## Review Feedback

*[This section is empty initially.]*

---

## Objectives & Success Criteria

- All core leaf fields have `postHydrate()` override that sets their default widget.
- No new Options classes created — all reuse `FieldOptions`.
- Widget assignment logic moved from constructor inline code to `postHydrate()`.
- Array path and Options path both converge on `postHydrate()`.
- All existing tests pass without modification.

**Implementation command**: `spec-kitty implement WP04 --base WP02`

## Context & Constraints

- **Data model**: See `kitty-specs/001-typed-options-migration/data-model.md` — "Leaf Fields" section for widget mappings.
- **FR-007**: Existing test files MUST NOT be changed.
- **Pattern**: Each `postHydrate()` sets default widget if not already set: `if ($this->widget === null) { $this->widget = new XWidget(); }`
- **Critical**: The existing constructor may already set the widget inline. You must MOVE that logic to `postHydrate()` and ensure the constructor calls `postHydrate()` (which it should if `constructFromOptions` or the array path already converges there from WP01/WP02 work).

## Subtasks & Detailed Guidance

### Subtask T017 – postHydrate for String, Integer, Float, Decimal

- **Purpose**: Add default TextInput widget via `postHydrate()`.
- **Steps**:
  1. **StringField** (`src/Fields/Core/StringField.php` or `src/Fields/Simple/StringField.php`):
     - Add `postHydrate()`: set `$this->widget = new TextInput()` if null
     - Remove any inline widget assignment from constructor
  2. **IntegerField** (`src/Fields/Core/IntegerField.php`):
     - Add `postHydrate()`: set `$this->widget = new TextInput()` if null
  3. **FloatField** (`src/Fields/Core/FloatField.php`):
     - Add `postHydrate()`: set `$this->widget = new TextInput()` if null
  4. **DecimalField**: Inherits from FloatField — verify it picks up the parent's `postHydrate()`. Only override if it needs different behavior.
- **Files**: `StringField.php`, `IntegerField.php`, `FloatField.php`, `DecimalField.php`
- **Parallel?**: Yes
- **Notes**: Find the actual file locations — some may be in `Core/`, others in `Simple/`. Use the project's PSR-4 mapping.

### Subtask T018 – postHydrate for Boolean, SelectBase, SelectMultiple, Radio

- **Purpose**: Add default widgets for selection/boolean fields.
- **Steps**:
  1. **BooleanField** (`src/Fields/Core/BooleanField.php`):
     - `postHydrate()`: set `$this->widget = new CheckboxInput()` if null
  2. **SelectFieldBase** (`src/Fields/Core/SelectFieldBase.php`):
     - `postHydrate()`: set default `option_widget` to `Option` if null
  3. **SelectMultipleField** (`src/Fields/Core/SelectMultipleField.php`):
     - `postHydrate()`: set `$this->widget = new Select(['multiple' => true])` if null
  4. **RadioField** (find location):
     - `postHydrate()`: set `$this->widget = new ListWidget('ul', false)` if null; set option widget to `RadioInput`
- **Files**: `BooleanField.php`, `SelectFieldBase.php`, `SelectMultipleField.php`, `RadioField.php`
- **Parallel?**: Yes
- **Notes**: `RadioField` has compound widget setup (ListWidget + RadioInput). Read existing constructor carefully.

### Subtask T019 – postHydrate for Password, File, Hidden, TextArea, Submit

- **Purpose**: Add default widgets for simple input fields.
- **Steps**:
  1. **PasswordField**: `postHydrate()` → `PasswordInput`
  2. **FileField**: `postHydrate()` → `FileInput`
  3. **HiddenField**: `postHydrate()` → `HiddenInput`
  4. **TextAreaField**: `postHydrate()` → `TextArea`
  5. **SubmitField**: `postHydrate()` → `SubmitInput`
  6. For each: remove inline widget assignment from constructor if present
- **Files**: All in `src/Fields/Simple/` (verify paths)
- **Parallel?**: Yes
- **Notes**: These are the simplest cases — single widget assignment each.

### Subtask T020 – Run Full Test Suite

- **Purpose**: Verify backward compatibility after moving widget logic.
- **Steps**:
  1. Run `vendor/bin/phpunit`
  2. Zero failures
  3. Pay special attention to widget rendering tests — they exercise the exact code being moved
- **Parallel?**: No

## Risks & Mitigations

- **Moving widget assignment breaks timing**: If other constructor logic depends on the widget being set during construction (before `postHydrate`), moving it could break behavior. Read each constructor carefully.
- **Inheritance chains**: `DecimalField` extends `FloatField` — don't duplicate `postHydrate()` unless the widget differs.

## Review Guidance

- Verify each `postHydrate()` uses the correct widget class per data-model.md.
- Verify no inline widget assignment remains in constructors.
- Verify the array path calls `postHydrate()`.

## Activity Log

- 2026-01-30T00:00:00Z – system – lane=planned – Prompt created.
