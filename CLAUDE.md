# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

php-wtforms is a PHP port of the Python WTForms library. It provides declarative form building and validation with a focus on flexibility and extensibility. The library follows PHP 5.5+ standards and uses PSR-4 autoloading.

## Development Commands

### Docker Setup (Recommended)

The project includes a Docker Compose setup for isolated development across multiple PHP versions:

```bash
# Start containers
docker-compose up -d

# Run tests (PHP 5.6 - production environment)
./docker/scripts/test.sh php56

# Run tests on PHP 7.4
./docker/scripts/test.sh php74

# Run tests across all PHP versions
./docker/scripts/test-all.sh

# Run composer commands
./docker/scripts/composer.sh php56 install

# Open interactive shell
./docker/scripts/shell.sh php56
```

See [DOCKER.md](DOCKER.md) for complete Docker documentation.

### Testing (Native)
```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit --testsuite="WTForms Common Tests"
./vendor/bin/phpunit --testsuite="WTForms Field Tests"
./vendor/bin/phpunit --testsuite="WTForms Validators Test"
./vendor/bin/phpunit --testsuite="WTForms Widgets Tests"
./vendor/bin/phpunit --testsuite="WTForms CSRF Tests"
./vendor/bin/phpunit --testsuite="WTForms Quirks Tests"

# Run tests with coverage (generates reports in tests/_reports/)
./vendor/bin/phpunit --coverage-html tests/_reports/coverage
```

### Dependencies
```bash
# Install dependencies
composer install

# Update dependencies
composer update
```

## Architecture

### Core Components

**Form (src/Form.php)**: The base class for all forms. Forms are declarative - fields are added to the form instance during construction via `__set()` magic method. Forms use traits for ArrayAccess and Iterator functionality, making them iterable and array-accessible.

**Field (src/fields/core/Field.php)**: Base class for all form fields. Fields are self-contained units that:
- Handle data processing (incoming formdata vs. object data)
- Run validation chains (pre_validate → validators → post_validate)
- Render themselves via widgets
- Maintain their own error state
- Use a meta object reference for configuration

**Validator (src/validators/Validator.php)**: Base class for validators. All validators must implement:
- `__construct($message, array $options)`
- `__invoke(Form $form, Field $field, $message = "")`
- Can throw `ValidationError` or `StopValidation` exceptions

**Widget (src/widgets/core/Widget.php)**: Base class for HTML rendering. Widgets are invokable objects that take a field and render options, returning HTML strings.

**DefaultMeta (src/DefaultMeta.php)**: Configuration class that controls form-level behavior including CSRF settings and field rendering strategy.

### Package Structure

```
src/
├── Form.php                 # Base form class
├── fields/
│   ├── core/               # Base field types (Field, StringField, IntegerField, etc.)
│   ├── html5/              # HTML5-specific fields (DateField, EmailField, URLField, etc.)
│   └── simple/             # Simple fields (TextAreaField, PasswordField, FileField, etc.)
├── validators/             # Validation classes
├── widgets/
│   ├── core/               # Base widget types
│   └── html5/              # HTML5-specific widgets
├── csrf/
│   ├── core/               # CSRF base classes
│   └── session/            # Session-based CSRF implementation
└── exceptions/             # Custom exception types
```

### Key Patterns

**Form Lifecycle**:
1. Form instantiation: `new MyForm($options)` or `MyForm::create($options)`
2. Field initialization via `__construct()` adding fields as properties
3. Processing: `process($options)` called (automatically in `create()`)
   - Fields process formdata (e.g., `$_POST`)
   - Fields process object data or defaults
4. Validation: `validate()` runs validator chains on all fields
5. Data access: `$form->data` returns array of all field data
6. Error access: `$form->errors` returns array of field errors

**Field Processing Priority**:
1. formdata (e.g., `$_POST` data) - highest priority
2. Explicit field values in options (named parameters)
3. Object properties (`obj` parameter)
4. data array
5. Field defaults - lowest priority

**Validation Chain**:
Each field runs: `preValidate()` → field validators → extra validators (from form) → `postValidate()`
- Validators can throw `ValidationError` to add errors and continue
- Validators can throw `StopValidation` to halt the chain
- Extra validators are form-level methods named `validate_<fieldname>`

**Form as Container**:
Forms use `__get()` and `__set()` magic methods to treat fields as properties. This enables:
- `$form->username` to access field objects
- `$form->username = new StringField()` to add fields
- `$form->data` and `$form->errors` as computed properties

### Dependencies

- **nesbot/carbon**: Used for date/time field handling (version 1.18 to <2.0.0)
- **phpunit/phpunit**: Testing framework (4.8.* for development)

### Testing Structure

Tests mirror the src/ structure and use PHPUnit with supporting classes in `tests/supporting_classes/`:
- `DummyField`: Minimal field implementation for testing
- `DummyCSRF`: Testing CSRF without actual session requirements

The `tests/quirks/` directory contains tests for edge cases and unusual interactions between components (e.g., FormField inside FieldList).

## Extending the Library

**Creating Custom Fields**:
Extend `WTForms\Fields\Core\Field` and override:
- `processFormData(array $valuelist)` - handle incoming POST data
- `processData($value)` - handle object/default data
- Optionally set `$widget` property to custom widget instance

**Creating Custom Validators**:
Extend `WTForms\Validators\Validator` and implement:
- Constructor that accepts message and options
- `__invoke(Form $form, Field $field, $message = "")` that throws `ValidationError` on failure
- Optionally set `$field_flags` to mark fields (e.g., `['required']`)

**Creating Custom Widgets**:
Extend `WTForms\Widgets\Core\Widget` and implement:
- `__invoke($field, array $options)` returning HTML string
- Merge `$field->render_kw` with `$options` for final attributes
