# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

php-wtforms is a PHP port of Python's WTForms library - a declarative forms framework for defining, validating, rendering, and handling web forms. Current version: 0.5.2, requires PHP >= 5.5.

## Commands

```bash
# Install dependencies
composer install

# Run all tests
phpunit --configuration ./phpunit.xml --bootstrap ./vendor/autoload.php

# Run a specific test file
phpunit --configuration ./phpunit.xml --bootstrap ./vendor/autoload.php tests/fields/StringFieldTest.php

# Run tests with coverage (outputs to tests/_reports/)
phpunit --configuration ./phpunit.xml --bootstrap ./vendor/autoload.php --coverage-html tests/_reports/coverage/
```

## Architecture

### Namespace Structure (PSR-4)
- `WTForms\` → `src/`
- `WTForms\Fields\` → `src/fields/` (core/, simple/, html5/)
- `WTForms\Validators\` → `src/validators/`
- `WTForms\Widgets\` → `src/widgets/` (core/, html5/)
- `WTForms\CSRF\` → `src/csrf/`
- `WTForms\Exceptions\` → `src/exceptions/`

### Core Components

**Form** (`src/Form.php`): Base class implementing `ArrayAccess` and `Iterator`. Fields are added in constructor and accessed via `$form->fieldname` or `$form['fieldname']`.

**Fields** (`src/fields/`): Data containers with validation. Base class is `Field`. Organized into:
- `core/`: StringField, IntegerField, SelectField, DateField, FieldList, FormField, etc.
- `simple/`: PasswordField, FileField, HiddenField, TextAreaField, SubmitField
- `html5/`: EmailField, URLField, TelField, SearchField, DateTimeLocalField, range fields

**Validators** (`src/validators/`): Callable objects implementing `__invoke(Form $form, Field $field, $message)`. Throw `ValidationError` on failure. Includes: InputRequired, DataRequired, Length, Email, URL, IPAddress, NumberRange, Regexp, EqualTo, AnyOf, NoneOf, etc.

**Widgets** (`src/widgets/`): Callable objects that render fields as HTML. Each field type has a default widget. `DefaultMeta.renderField()` orchestrates rendering.

**CSRF** (`src/csrf/`): Plugin architecture with `CSRF` base class and `SessionCSRF` implementation. Auto-attached to forms when `meta.csrf = true`.

### Key Patterns

**Declarative Form Definition**:
```php
class LoginForm extends Form {
    public function __construct(array $options = []) {
        parent::__construct($options);
        $this->username = new StringField(["validators" => [new InputRequired()]]);
        $this->password = new PasswordField(["validators" => [new InputRequired()]]);
    }
}
```

**Validation Pipeline**: Form receives data via `formdata` option → Fields process/coerce data → `$form->validate()` runs validators → Errors collected in `$form->errors`.

**Flags** (`src/Flags.php`): Dynamic boolean container that returns `false` for non-existent flags (e.g., `$field->flags->required`).

## Testing

Tests mirror source structure in `tests/`. Supporting test fixtures are in `tests/supporting_classes/`. PHPUnit 4.8.x is used.

## Future Direction

See `docs/plans/conformity-prd.md` for the "Conformity" roadmap - a PHP 8+ evolution using attributes, Doctrine integration, and Symfony compatibility.
