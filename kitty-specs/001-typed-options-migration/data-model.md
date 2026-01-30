# Data Model: Typed Options Migration

**Feature**: 001-typed-options-migration
**Date**: 2026-01-30

## Options Class Hierarchy

### Base

- **ArrayOptions** (`src/Internal/Types/ArrayOptions.php`) — Abstract base. Provides `validate()` (override for guards), abstract `hydrateObject(object $obj): object` (pure assignment), array access, `from()` factory.

### Field Options

- **FieldOptions** (`src/Fields/Options/FieldOptions.php`) — Base field config: label, description, validators, filters, id, default, render_kw.
- **FieldListOptions** extends FieldOptions — inner_field, min_entries, max_entries. Validate: no filters, inner_field required.
- **SelectFieldOptions** extends FieldOptions — choices, coerce. PostHydrate: default Select widget, identity coerce.
- **DateTimeFieldOptions** extends FieldOptions — format. PostHydrate: strftime-to-Carbon conversion, default TextInput.
- **FormFieldOptions** extends FieldOptions (NEW) — form_class (class-string, required), separator (string, default '-'). Validate: form_class required, no filters, no validators. PostHydrate: default TableWidget.

### Validator Options

- **LengthOptions** — min, max. Validate: at least one present, min <= max.
- **NumberRangeOptions** — min, max. No validation needed.
- **AnyOfOptions** — values (array), formatter (callable). Validate: values is array, formatter callable.
- **OptionalOptions** — strip_whitespace (bool). No validation.
- **RegexpOptions** — regex (string). Validate: valid regex.
- **UUIDOptions** extends RegexpOptions — inherits + default UUID pattern.
- **IPAddressOptions** — ip_type (int flags). Validate: valid flag value.
- **EqualToOptions** — fieldname (string). Validate: fieldname present.
- **RequiredIfOptions** — fieldname (string). Validate: fieldname present.

### Widget Options (all NEW)

- **InputOptions** — type (string).
- **PasswordInputOptions** — hide_value (bool).
- **ListWidgetOptions** — html_tag (string), prefix_label (bool).
- **TableWidgetOptions** — with_table_tag (bool).
- **NumberInputOptions** — step, min, max (string|int|null).
- **RangeInputOptions** — step (string|int|null).

### CSRF Options (NEW)

- **CSRFTokenFieldOptions** — csrf_impl (class-string|CSRF instance, required). Validate: csrf_impl present and valid. PostHydrate: inherits HiddenField defaults.

### Form Options (NEW)

- **FormOptions** — meta (array|DefaultMeta), prefix (string), formdata (array), obj (object|null), data (array|null). Validate: minimal. Hydrate: normalizes prefix (appends '-').

## Lifecycle Contract

Every constructable class follows:

1. `validate()` — Pure assertions on the Options object. Throws TypeError or ValidationError. No side effects.
2. `hydrateObject(object $obj): object` — Pure property assignment from Options to target. No defaults, no derived values.
3. `postHydrate(): void` — Hook on the target class for derived state: default widgets, label promotion, computed properties. No-op in base classes; subclasses override without calling parent.

## Leaf Fields (postHydrate only, no new Options class)

Each sets its default widget in `postHydrate()`:

| Field | Default Widget |
|-------|---------------|
| StringField | TextInput |
| IntegerField | TextInput |
| FloatField | TextInput |
| DecimalField | (inherits FloatField) |
| BooleanField | CheckboxInput |
| SelectFieldBase | Option (option_widget) |
| SelectMultipleField | Select(multiple: true) |
| RadioField | ListWidget('ul', false) + RadioInput |
| PasswordField | PasswordInput |
| FileField | FileInput |
| HiddenField | HiddenInput |
| TextAreaField | TextArea |
| SubmitField | SubmitInput |
| EmailField | EmailInput |
| URLField | URLInput |
| TelField | TelInput |
| SearchField | SearchInput |
| DateTimeLocalField | DateTimeLocalInput |
| Range fields | RangeInput |
