# Typed Options Migration Design

Migrate all constructor options arrays to typed `ArrayOptions` subclasses with a three-phase construction lifecycle.

## Lifecycle

Every class that accepts constructor options follows this lifecycle:

1. **`validate()`** — Pure assertions on the Options object. Throws `TypeError` or `ValidationError`. No side effects.
2. **`hydrateObject(object $obj)`** — Pure property assignment from Options to target. No defaults, no derived values.
3. **`postHydrate()`** — Hook on the target class for derived state: default widgets, label promotion, computed properties.

The array-based constructor path is marked `@deprecated` but kept for backward compatibility until a future major version.

## Directory Structure

```
src/
  Fields/Options/      (existing — FieldOptions, FieldListOptions, SelectFieldOptions, DateTimeFieldOptions)
  Validators/Options/  (existing — LengthOptions, NumberRangeOptions, AnyOfOptions, etc.)
  Widgets/Options/     (new — InputOptions, PasswordInputOptions, ListWidgetOptions, etc.)
  CSRF/Options/        (new — CSRFTokenFieldOptions)
  Options/             (new — FormOptions)
```

## Naming Conventions

- Options class: `{ClassName}Options` in a sibling `Options/` directory
- PHPStan type alias: `@phpstan-type {ClassName}OptionsArray array{...}` on the Options class
- `hydrateObject` docblock: param typed to the specific target class
- `postHydrate`: always `protected`, no parameters, void return

## Per-File Checklist

1. Implement `validate()` with guard clauses moved from constructor
2. Implement `hydrateObject()` with pure property assignments
3. Update constructor signature to `array|XOptions`
4. Add `postHydrate()` override if the class has derived state
5. Remove moved logic from constructor body
6. Mark array path `@deprecated`
7. Run tests

## Phase 1: Base Classes

Wire up `constructFromOptions` and `postHydrate` hooks on base classes.

| Class | File | Work |
|-------|------|------|
| Field | `src/Fields/Core/Field.php` | **Done.** `constructFromOptions`, `collectFieldFlags`, `postHydrate` hook. |
| Validator | `src/Validators/Validator.php` | Add `constructFromOptions` + `postHydrate` hook, mirroring Field. |
| Widget | `src/Widgets/Core/Widget.php` | Add `constructFromOptions` + `postHydrate` hook. |

## Phase 2: Tier 1 — Wire Up Existing Options Classes

These classes already have Options classes. Add `validate()`, `hydrateObject()`, update constructors.

### Fields

| Class | Options Class | validate() | hydrateObject() | postHydrate() |
|-------|--------------|------------|-----------------|---------------|
| FieldList | FieldListOptions | **Done.** inner_field/filters checks | **Done.** inner_field, min/max_entries | **Done.** Default ListWidget |
| SelectField | SelectFieldOptions | None needed | choices, coerce | Default Select widget, identity coerce |
| DateTimeField | DateTimeFieldOptions | None needed | format | strftime-to-Carbon conversion, default TextInput |

### Validators

| Class | Options Class | validate() | hydrateObject() | postHydrate() |
|-------|--------------|------------|-----------------|---------------|
| Length | LengthOptions | min/max present, min <= max | min, max | None |
| NumberRange | NumberRangeOptions | None needed | min, max | None |
| AnyOf | AnyOfOptions | values is array, formatter callable | values, formatter | None |
| Optional | OptionalOptions | None needed | strip_whitespace | None |
| Regexp | RegexpOptions | regex is valid | regex | None |
| UUID | UUIDOptions | Inherits Regexp | Inherits + default pattern | None |
| IPAddress | IPAddressOptions | ip_type is valid flag | ip_type | None |
| EqualTo | EqualToOptions | fieldname present | fieldname | None |
| RequiredIf | RequiredIfOptions | fieldname present | fieldname | None |

## Phase 3: Tier 2 — postHydrate Only (No New Options Classes)

These fields reuse `FieldOptions`. Only add `postHydrate()` override for default widget.

| Class | postHydrate() sets |
|-------|-------------------|
| StringField | TextInput |
| IntegerField | TextInput |
| FloatField | TextInput |
| DecimalField | Inherits FloatField |
| BooleanField | CheckboxInput |
| SelectFieldBase | Default option_widget (Option) |
| SelectMultipleField | Select(multiple: true) |
| RadioField | ListWidget('ul', false) + RadioInput option widget |
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

## Phase 4: Tier 3 — New Options Classes

### FormFieldOptions (new)

- File: `src/Fields/Options/FormFieldOptions.php`
- Properties: `form_class` (class-string\<Form\>, required), `separator` (string, default '-')
- `validate()`: TypeError if form_class missing, filters provided, or validators provided
- `hydrateObject()`: assigns form_class, separator
- `postHydrate()` on FormField: default TableWidget

### CSRFTokenFieldOptions (new)

- File: `src/CSRF/Options/CSRFTokenFieldOptions.php`
- Properties: `csrf_impl` (class-string\<CSRF\> | CSRF, required)
- `validate()`: checks csrf_impl present and valid
- `hydrateObject()`: assigns csrf_impl, instantiates if class-string
- `postHydrate()`: inherits HiddenField

### Widget Options Classes (new)

| Options Class | File | Properties |
|--------------|------|------------|
| InputOptions | `src/Widgets/Options/InputOptions.php` | type (string) |
| PasswordInputOptions | `src/Widgets/Options/PasswordInputOptions.php` | hide_value (bool) |
| ListWidgetOptions | `src/Widgets/Options/ListWidgetOptions.php` | html_tag (string), prefix_label (bool) |
| TableWidgetOptions | `src/Widgets/Options/TableWidgetOptions.php` | with_table_tag (bool) |
| NumberInputOptions | `src/Widgets/Options/NumberInputOptions.php` | step, min, max (string\|int\|null) |
| RangeInputOptions | `src/Widgets/Options/RangeInputOptions.php` | step (string\|int\|null) |

### FormOptions (new)

- File: `src/Options/FormOptions.php`
- Properties: `meta` (array\|DefaultMeta), `prefix` (string), `formdata` (array), `obj` (object\|null), `data` (array\|null)
- `validate()`: minimal
- `hydrateObject()`: assigns meta, normalizes prefix (appends '-'), stores formdata/obj/data
- Form gets its own `constructFromOptions` since it does not extend Field

## Phase 5: Deprecation

1. Add `@deprecated Use XOptions parameter instead` to all array-path branches
2. Update CLAUDE.md with the typed options pattern guidance

## Execution Notes

- Each phase is independently shippable
- Tests run after every file change
- Inheritance chains respected: base classes first, then leaves
- No new Options classes for parameterless constructors
