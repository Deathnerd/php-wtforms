# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- **PHP 7.4 baseline**: upgraded to PHP 7.4 with PHPUnit 9 and Carbon 2; typed properties throughout.
- **Typed options migration (spec 001)**: Options classes for fields, validators, widgets, and forms (`FormOptions`, `Form::constructFromOptions`, `FormFieldOptions`, `CSRFTokenFieldOptions`) hydrated via `hydrateObject`/`postHydrate`; array-option construction kept with deprecation annotations.
- Directories renamed to PascalCase for simpler PSR-4 autoloading; Symfony formatting rules and 2-space indentation applied across the codebase.

### Added

- PHPStan configuration (level 5), a PHPStan extension for validators and type-safe `ArrayOptions`, and PHPDoc generics for typed field data.
- Psalm setup and a typing investigation covering fields, validators, widgets, forms, and taint analysis.
- Gettext support scaffolding for translations (unfinished).
- Job Application Form integration test.

### Fixed

- String labels are promoted to `Label` objects in the `Field` constructor.
- `Form::__set` uses the field's `short_name` for `name`/`id`, not the property name.
- PHP `TypeError` in `ArrayOptions::hydrateProperties` is rethrown as a WTForms `TypeError`.
