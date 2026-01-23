# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

php-wtforms is a PHP port of Python's WTForms library - a declarative forms framework for defining, validating, rendering, and handling web forms. Requires PHP >= 7.4.

## Commands

### Using Docker (recommended)

```bash
# Run tests on PHP 7.4
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpunit

# Run tests on PHP 5.6 (legacy baseline)
docker-compose -f .devcontainer/docker-compose.yml run --rm php56 vendor/bin/phpunit

# Run with coverage
docker-compose -f .devcontainer/docker-compose.yml run --rm -e XDEBUG_MODE=coverage php74 vendor/bin/phpunit --coverage-html tests/_reports/coverage/

# Install dependencies
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 composer install
```

### Local PHP

```bash
composer install
vendor/bin/phpunit
```

## Static Analysis (PHPStan)

```bash
# Run PHPStan (level 5)
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=512M

# Export errors to JSON for analysis
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=512M --error-format=json 2>/dev/null > phpstan-errors.json

# Parse and summarize errors
uv run scripts/parse_phpstan.py phpstan-errors.json

# View errors grouped by type
uv run scripts/parse_phpstan.py phpstan-errors.json --by-identifier

# View specific error type
uv run scripts/parse_phpstan.py phpstan-errors.json --by-identifier property.notFound

# View errors grouped by file
uv run scripts/parse_phpstan.py phpstan-errors.json --by-file
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

Tests mirror source structure in `tests/`. Supporting test fixtures are in `tests/supporting_classes/`. PHPUnit 9.x is used. Bootstrap file `tests/bootstrap.php` handles output buffering for session tests.

## Future Direction

See `docs/plans/conformity-prd.md` for the "Conformity" roadmap - a PHP 8+ evolution using attributes, Doctrine integration, and Symfony compatibility.
