# Conformity: Product Requirements Document

## Overview

Conformity is a modern, declarative forms library for PHP 8+ that evolved from [deathnerd/php-wtforms](https://github.com/Deathnerd/php-wtforms). It uses native PHP 8 attributes to define forms, validation, and rendering templates in a single source of truth.

## Goals

- Modernize php-wtforms for PHP 8+ using attributes and constructor promotion
- Eliminate the `debug_backtrace` hack with proper reflection-based field hydration
- Add a template system via attributes for DRY rendering
- Build Doctrine integration to generate forms from entity metadata
- Integrate with Symfony ecosystem (Twig, Validator, CSRF)

## Non-Goals

- Laravel/Eloquent support (no meaningful model metadata to reflect on)
- Backwards compatibility with php-wtforms 0.x

---

## Package Structure

```
deathnerd/conformity          — Core library, framework-agnostic
deathnerd/conformity-bundle   — Symfony integration + Doctrine magic
```

---

## Core Library (`deathnerd/conformity`)

### Form Definition

Forms use constructor property promotion with attributes for validators:

```php
class LoginForm extends Form {
    public function __construct(
        #[Required('Username is required')]
        #[Length(min: 3, max: 20)]
        public StringField $username,

        #[Required]
        public PasswordField $password,
    ) {
        parent::__construct();
    }
}
```

The base `Form` constructor uses reflection to:
1. Read constructor parameters
2. Extract validator attributes from each parameter
3. Bind validators to their respective fields
4. Wire up field-to-form references

### Template System

Templates can be specified at multiple levels via the `#[Template]` attribute:

```php
#[Template('forms/bootstrap5/field.html.twig')]
class StringField extends Field {}

#[Template('forms/my-custom-form.html.twig')]
class LoginForm extends Form {
    public function __construct(
        #[Template('forms/fancy-username.html.twig')]
        public StringField $username,
        // ...
    ) {}
}
```

Resolution order (most specific wins):
1. Field instance attribute
2. Form attribute
3. Field class attribute
4. Global default

Theme packs apply a template set to all fields:

```php
#[Theme('bootstrap5')]
class LoginForm extends Form {}
```

### Rendering

Preserve the clean rendering from php-wtforms:

```php
<?= $form->username->label ?>
<?= $form->username ?>
```

Fields implement `__toString()` which resolves the template and renders.

### Validators

Port existing validators from php-wtforms, convert to attributes:

- `#[Required]` / `#[InputRequired]`
- `#[Length(min, max)]`
- `#[Email]`
- `#[URL]`
- `#[Regexp(pattern)]`
- `#[NumberRange(min, max)]`
- `#[EqualTo(field)]`
- `#[AnyOf(values)]`
- `#[NoneOf(values)]`
- `#[Optional]`

### Fields

Port existing fields from php-wtforms:

**Core:** `StringField`, `IntegerField`, `FloatField`, `DecimalField`, `BooleanField`, `DateField`, `DateTimeField`, `SelectField`, `SelectMultipleField`, `RadioField`

**Simple:** `PasswordField`, `TextAreaField`, `HiddenField`, `SubmitField`, `FileField`

**HTML5:** `EmailField`, `URLField`, `TelField`, `SearchField`, `DateField`, `DateTimeLocalField`, `IntegerRangeField`, `DecimalRangeField`

---

## Symfony Bundle (`deathnerd/conformity-bundle`)

### Doctrine Entity Integration

Generate forms from Doctrine entity metadata:

```php
// New entity
$form = Form::fromEntity(User::class);

// Existing entity (pre-populated)
$form = Form::fromEntity($user);
```

Type mapping from Doctrine to Conformity:

| Doctrine Type | Field Type | Inferred Validators |
|---------------|------------|---------------------|
| `string` | `StringField` | `Length(max: column length)` |
| `text` | `TextAreaField` | |
| `integer` | `IntegerField` | |
| `boolean` | `BooleanField` | |
| `datetime` | `DateTimeField` | |
| `ManyToOne` | `SelectField` | choices from related entity |
| `ManyToMany` | `SelectMultipleField` | choices from related entity |
| `nullable: true` | | `Optional` |
| `nullable: false` | | `Required` |

Fluent API for customization:

```php
$form = Form::fromEntity(User::class)
    ->exclude('createdAt', 'id')
    ->override('bio', fn($field) => $field->withTemplate('markdown-editor.twig'))
    ->addField('password_confirm', new PasswordField([...]));
```

Entity population:

```php
$user = $form->toEntity();           // Create new
$form->populateEntity($user);        // Update existing
```

### Symfony Validator Bridge

Map Symfony `#[Assert\*]` attributes to Conformity validators:

| Symfony Constraint | Conformity Validator |
|--------------------|----------------------|
| `#[Assert\NotBlank]` | `Required` |
| `#[Assert\Email]` | `Email` |
| `#[Assert\Length]` | `Length` |
| `#[Assert\Range]` | `NumberRange` |

### Twig Integration

Optional helpers (fields already work via `__toString()`):

```twig
{{ conformity_form(form) }}
{{ conformity_field(form.username) }}
```

### Service Integration

- Forms autowirable with dependencies
- CSRF token integration with Symfony's token manager

---

## Future Considerations

These are not in scope for initial release but worth keeping in mind:

- JSON schema generation for frontend validation
- API mode (return validation errors as JSON)
- PSR-7 request processing in core library

---

## References

- Original php-wtforms: https://github.com/Deathnerd/php-wtforms
- Python WTForms: https://wtforms.readthedocs.io/
- WTForms-Alchemy (SQLAlchemy integration): https://wtforms-alchemy.readthedocs.io/
