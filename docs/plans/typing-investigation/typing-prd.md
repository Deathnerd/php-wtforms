# Thorough Static Analysis PRD
## PHP-WTForms Comprehensive Typing Initiative

**Version:** 1.0.0-investigation
**Status:** Investigation Phase
**Last Updated:** 2026-01-26

---

## Executive Summary

This document defines the requirements for achieving **maximum static analysis coverage** in php-wtforms using PHPStan, Psalm, and taint analysis. The goal is to make the codebase as thoroughly typed and analyzed as possible while maintaining PHP 7.4 compatibility.

**Target Outcomes:**
- PHPStan level 9 (max) with zero errors
- Psalm level 1 (max) with zero errors
- Full taint analysis for security-sensitive code paths
- Comprehensive generics/template usage
- Full type coverage for all public APIs

---

## How to Use This Document

This PRD is designed for iterative investigation using Claude Code. Each section contains:

1. **Requirements** - What needs to be achieved
2. **Investigation Questions** - Questions that must be answered before implementation
3. **Investigation Status** - Track completion with checkboxes

Work through each section, answering questions by examining the codebase, running analysis tools, and researching documentation.

---

## Table of Contents

1. [Current State Assessment](#1-current-state-assessment)
2. [PHPStan Configuration & Baseline](#2-phpstan-configuration--baseline)
3. [Psalm Setup & Configuration](#3-psalm-setup--configuration)
4. [Taint Analysis](#4-taint-analysis)
5. [Field Classes Typing](#5-field-classes-typing)
6. [Validator Classes Typing](#6-validator-classes-typing)
7. [Widget Classes Typing](#7-widget-classes-typing)
8. [Form & Meta Typing](#8-form--meta-typing)
9. [CSRF & Security Typing](#9-csrf--security-typing)
10. [Generics & Templates Strategy](#10-generics--templates-strategy)
11. [Internal Utilities Typing](#11-internal-utilities-typing)
12. [Testing Type Coverage](#12-testing-type-coverage)

---

## 1. Current State Assessment

### 1.1 Requirements

Before making changes, we must understand the current typing state:

- Current PHPStan level and error count
- Existing type annotations (native + PHPDoc)
- Areas with missing or incomplete typing
- PHP version constraints affecting type options
- Existing generics/template usage patterns

### 1.2 Investigation Questions

**Q1.1:** What is the current PHPStan configuration? Run `cat phpstan.neon` or `cat phpstan.neon.dist` and document the level, paths, and any ignores/baselines.

**Q1.2:** What is the current PHPStan error count at each level (5 through 9)? Run PHPStan at each level and document the error counts.

**Q1.3:** Is there a PHPStan baseline file? If so, how many errors are baselined and what categories?

**Q1.4:** What percentage of methods have native return type declarations? Sample 10 files and estimate coverage.

**Q1.5:** What percentage of methods have `@param` and `@return` PHPDoc annotations? Sample 10 files.

**Q1.6:** Are there any existing `@template` or `@phpstan-*` annotations? Search for their usage patterns.

**Q1.7:** What PHP 7.4 type features are available vs. PHP 8.0+ features we cannot use? Document the constraints.

**Q1.8:** Are there any `mixed` types, `@var` without types, or `/** @noinspection */` suppressions?

**Q1.9:** What composer dev dependencies exist for static analysis? Check `composer.json`.

**Q1.10:** Are there any custom PHPStan extensions or rules configured?

### 1.3 Investigation Status

- [x] Q1.1 - PHPStan configuration documented
- [x] Q1.2 - Error counts at each level recorded
- [x] Q1.3 - Baseline status checked
- [x] Q1.4 - Native return type coverage estimated
- [x] Q1.5 - PHPDoc coverage estimated
- [x] Q1.6 - Template/generic usage patterns documented
- [x] Q1.7 - PHP 7.4 type constraints documented
- [x] Q1.8 - Suppressions and weak types identified
- [x] Q1.9 - Dev dependencies documented
- [x] Q1.10 - Custom extensions identified

---

## 2. PHPStan Configuration & Baseline

### 2.1 Requirements

Configure PHPStan for maximum strictness:

- Level 9 (max) analysis
- No baseline (all errors fixed)
- Strict rules extension enabled
- Bleeding edge rules enabled
- Custom rules for project-specific patterns
- CI integration for regression prevention

### 2.2 Investigation Questions

**Q2.1:** What are the differences between PHPStan levels 5-9? Document what each level adds.

**Q2.2:** What does `phpstan/phpstan-strict-rules` add? Should we enable it?

**Q2.3:** What does `phpstan/phpstan-deprecation-rules` add? Is it useful for our codebase?

**Q2.4:** What bleeding edge features are available? Run `phpstan analyse --generate-baseline` and examine.

**Q2.5:** What PHPStan extensions exist for Carbon, PHPUnit, or other dependencies we use?

**Q2.6:** How should we handle the `__get` and `__set` magic methods that PHPStan struggles with?

**Q2.7:** What `@phpstan-ignore-*` annotations are acceptable vs. which indicate real issues?

**Q2.8:** How do we configure PHPStan to understand our `ArrayAccess` implementations on Form?

**Q2.9:** Should we use `checkMissingIterableValueType: true` and `checkGenericClassInNonGenericObjectType: true`?

**Q2.10:** How do we add PHPStan to CI (GitHub Actions) to prevent regressions?

### 2.3 Investigation Status

- [x] Q2.1 - Level differences documented
- [x] Q2.2 - Strict rules evaluated
- [x] Q2.3 - Deprecation rules evaluated
- [x] Q2.4 - Bleeding edge features reviewed
- [x] Q2.5 - Relevant extensions identified
- [x] Q2.6 - Magic method handling strategy defined
- [x] Q2.7 - Acceptable ignores documented
- [x] Q2.8 - ArrayAccess configuration solved
- [x] Q2.9 - Iterable/generic strictness decided
- [x] Q2.10 - CI integration planned

---

## 3. Psalm Setup & Configuration

### 3.1 Requirements

Add Psalm as a complementary analysis tool:

- Level 1 (max) analysis
- Different error detection than PHPStan
- Taint analysis capabilities
- Type inference comparison
- Integration with existing codebase

### 3.2 Investigation Questions

**Q3.1:** How do we install and initialize Psalm? What is the command and what files are created?

**Q3.2:** What are Psalm's error levels (1-8)? How do they compare to PHPStan levels?

**Q3.3:** What errors does Psalm catch that PHPStan doesn't, and vice versa?

**Q3.4:** How do we configure Psalm to work alongside PHPStan without conflicts?

**Q3.5:** What Psalm plugins exist for our dependencies (Carbon, PHPUnit)?

**Q3.6:** How does Psalm handle generics differently than PHPStan? Any syntax differences?

**Q3.7:** What is `@psalm-*` vs `@phpstan-*` annotation compatibility?

**Q3.8:** How do we handle cases where Psalm and PHPStan disagree on typing?

**Q3.9:** Should we use Psalm's `--alter` mode for automatic fixes?

**Q3.10:** How do we add Psalm to CI alongside PHPStan?

### 3.3 Investigation Status

- [ ] Q3.1 - Psalm installation documented
- [ ] Q3.2 - Psalm levels understood
- [ ] Q3.3 - Psalm vs PHPStan differences documented
- [ ] Q3.4 - Coexistence configuration designed
- [ ] Q3.5 - Psalm plugins identified
- [ ] Q3.6 - Generics syntax differences documented
- [ ] Q3.7 - Annotation compatibility understood
- [ ] Q3.8 - Disagreement resolution strategy defined
- [ ] Q3.9 - Alter mode evaluated
- [ ] Q3.10 - CI integration planned

---

## 4. Taint Analysis

### 4.1 Requirements

Implement comprehensive taint analysis for security:

- Track user input through the system
- Identify potential XSS vulnerabilities in HTML output
- Identify SQL injection risks (if any database usage)
- Mark sources, sinks, and sanitizers
- Document security-sensitive code paths

### 4.2 Investigation Questions

**Q4.1:** How does Psalm's taint analysis work? What are sources, sinks, and sanitizers?

**Q4.2:** What taint sources exist in php-wtforms? (Form input, raw_data, etc.)

**Q4.3:** What taint sinks exist? (HTML output in widgets, __toString methods)

**Q4.4:** What sanitization functions exist? (htmlspecialchars in rendering, etc.)

**Q4.5:** How do we annotate sources with `@psalm-taint-source`?

**Q4.6:** How do we annotate sinks with `@psalm-taint-sink`?

**Q4.7:** How do we mark sanitizers with `@psalm-taint-escape`?

**Q4.8:** Are there any existing XSS vulnerabilities in widget rendering?

**Q4.9:** How do we handle legitimate HTML output (e.g., form rendering)?

**Q4.10:** Does PHPStan have any taint analysis capabilities or extensions?

### 4.3 Investigation Status

- [ ] Q4.1 - Taint analysis concepts understood
- [ ] Q4.2 - Taint sources identified
- [ ] Q4.3 - Taint sinks identified
- [ ] Q4.4 - Sanitization points identified
- [ ] Q4.5 - Source annotation syntax learned
- [ ] Q4.6 - Sink annotation syntax learned
- [ ] Q4.7 - Escape annotation syntax learned
- [ ] Q4.8 - Existing vulnerabilities checked
- [ ] Q4.9 - Legitimate HTML handling designed
- [ ] Q4.10 - PHPStan taint options researched

---

## 5. Field Classes Typing

### 5.1 Requirements

Achieve complete typing for all Field classes:

- Base `Field` class with full generics
- All field subclasses with specific data types
- `FieldList` and `FormField` with proper generics
- Select fields with choice typing
- Date/time fields with Carbon/DateTime typing

### 5.2 Investigation Questions

**Q5.1:** What is the current generic structure of the base `Field` class? What is `TData` used for?

**Q5.2:** How should `$data` property be typed for each field type? (string for StringField, ?Carbon for DateField, etc.)

**Q5.3:** How do we type `FieldList<T>` where T is the contained field type?

**Q5.4:** How do we type `FormField<T>` where T is the contained form type?

**Q5.5:** What is the type of `$choices` in SelectField? How do we handle dynamic choices?

**Q5.6:** How do we handle `processFormData()` which receives `array<int, string>` from form submission?

**Q5.7:** What is the proper type for `$raw_data`? Is it `?array<int, string>`?

**Q5.8:** How do we type the `$widget` property given widgets are generic?

**Q5.9:** How should `$validators` be typed? `array<int, Validator>`?

**Q5.10:** What covariance/contravariance issues exist when overriding methods in subclasses?

### 5.3 Investigation Status

- [ ] Q5.1 - Base Field generic structure documented
- [ ] Q5.2 - Per-field data types defined
- [ ] Q5.3 - FieldList generics designed
- [ ] Q5.4 - FormField generics designed
- [ ] Q5.5 - SelectField choices typed
- [ ] Q5.6 - processFormData typing solved
- [ ] Q5.7 - raw_data type defined
- [ ] Q5.8 - Widget property typing solved
- [ ] Q5.9 - Validators array typed
- [ ] Q5.10 - Variance issues documented

---

## 6. Validator Classes Typing

### 6.1 Requirements

Complete typing for all validator classes:

- Base `Validator` class with options generics
- Type-safe validator construction
- Proper `__invoke` signature
- ValidationError exception typing

### 6.2 Investigation Questions

**Q6.1:** What is the current `@template TOptions` pattern in the Validator base class?

**Q6.2:** How do we type each validator's specific options? (e.g., Length needs min/max)

**Q6.3:** Should validators return void or have a more specific return type?

**Q6.4:** How do we type the `$message` parameter and interpolation?

**Q6.5:** What is the proper type for `$field_flags` array?

**Q6.6:** How do we handle validators that access `$field->data` with different types?

**Q6.7:** Should we use `@phpstan-assert` annotations for validators that guarantee types?

**Q6.8:** How do we type optional vs required field validation?

**Q6.9:** What is the proper exception type hierarchy for ValidationError?

**Q6.10:** How do we handle Regexp validator's pattern typing?

### 6.3 Investigation Status

- [ ] Q6.1 - TOptions template pattern documented
- [ ] Q6.2 - Per-validator options typed
- [ ] Q6.3 - Return type decided
- [ ] Q6.4 - Message interpolation typed
- [ ] Q6.5 - field_flags typed
- [ ] Q6.6 - Field data access typing solved
- [ ] Q6.7 - phpstan-assert usage evaluated
- [ ] Q6.8 - Optional/required handling typed
- [ ] Q6.9 - Exception hierarchy documented
- [ ] Q6.10 - Regexp pattern typed

---

## 7. Widget Classes Typing

### 7.1 Requirements

Complete typing for all widget classes:

- Base `Widget` class with field generics
- Input widgets with proper attribute typing
- Select/List widgets with options typing
- HTML output string typing

### 7.2 Investigation Questions

**Q7.1:** What is the current `@template TData` pattern in the Widget base class?

**Q7.2:** How do we ensure widgets only accept compatible field types?

**Q7.3:** What is the proper type for the `$options` array in `__invoke`?

**Q7.4:** How do we type HTML attribute arrays? `array<string, string|bool|int>`?

**Q7.5:** Should the return type be `string` or a more specific HTML string type?

**Q7.6:** How do we type `ListWidget` that renders collections of sub-widgets?

**Q7.7:** How do we type `Select` widget's rendering of choices?

**Q7.8:** What is the proper type for `$field_flags` in widgets?

**Q7.9:** How do we handle widgets that modify field state during rendering?

**Q7.10:** Are there any widgets with security-sensitive output (taint sinks)?

### 7.3 Investigation Status

- [ ] Q7.1 - TData template pattern documented
- [ ] Q7.2 - Field compatibility typing designed
- [ ] Q7.3 - Options parameter typed
- [ ] Q7.4 - HTML attributes typed
- [ ] Q7.5 - Return type decided
- [ ] Q7.6 - ListWidget typed
- [ ] Q7.7 - Select widget typed
- [ ] Q7.8 - field_flags typed
- [ ] Q7.9 - State modification typing handled
- [ ] Q7.10 - Taint sinks identified

---

## 8. Form & Meta Typing

### 8.1 Requirements

Complete typing for Form and DefaultMeta classes:

- Form with field access typing
- ArrayAccess and Iterator implementations
- Meta configuration typing
- Form population and validation typing

### 8.2 Investigation Questions

**Q8.1:** How do we type Form's `ArrayAccess` for dynamic field access?

**Q8.2:** How do we type Form's `Iterator` for field iteration?

**Q8.3:** What is the proper type for `$_fields` storage?

**Q8.4:** How do we handle magic `__get` for field access with proper types?

**Q8.5:** What is the type of `$data` and `$errors` on Form?

**Q8.6:** How do we type `populate_obj()` with generic objects?

**Q8.7:** What is the proper type for `$formdata` (request data input)?

**Q8.8:** How do we type the Meta class configuration options?

**Q8.9:** What is the type signature for `validate()`?

**Q8.10:** How do we type CSRF field integration in Form?

### 8.3 Investigation Status

- [ ] Q8.1 - ArrayAccess typed
- [ ] Q8.2 - Iterator typed
- [ ] Q8.3 - $_fields storage typed
- [ ] Q8.4 - Magic __get handling designed
- [ ] Q8.5 - data/errors typed
- [ ] Q8.6 - populate_obj generics designed
- [ ] Q8.7 - formdata input typed
- [ ] Q8.8 - Meta configuration typed
- [ ] Q8.9 - validate() signature defined
- [ ] Q8.10 - CSRF integration typed

---

## 9. CSRF & Security Typing

### 9.1 Requirements

Complete typing for CSRF and security components:

- CSRF base class typing
- SessionCSRF implementation typing
- Token generation and validation typing
- Secure time comparison typing

### 9.2 Investigation Questions

**Q9.1:** What is the type structure of the CSRF base class?

**Q9.2:** How do we type the token generation methods?

**Q9.3:** What is the proper type for session storage integration?

**Q9.4:** How do we type time-limit validation for CSRF tokens?

**Q9.5:** What security-sensitive operations need taint analysis?

**Q9.6:** How do we type `generate_csrf_token()` and `validate_csrf_token()`?

**Q9.7:** What is the type for CSRF field metadata?

**Q9.8:** How do we handle nullable tokens during validation?

**Q9.9:** Should `hash_equals` usage be typed for timing-safe comparison?

**Q9.10:** What exceptions does CSRF validation throw?

### 9.3 Investigation Status

- [ ] Q9.1 - CSRF base class typed
- [ ] Q9.2 - Token generation typed
- [ ] Q9.3 - Session integration typed
- [ ] Q9.4 - Time-limit validation typed
- [ ] Q9.5 - Taint analysis applied
- [ ] Q9.6 - Public API methods typed
- [ ] Q9.7 - Field metadata typed
- [ ] Q9.8 - Nullable handling designed
- [ ] Q9.9 - Timing-safe comparison typed
- [ ] Q9.10 - Exception types documented

---

## 10. Generics & Templates Strategy

### 10.1 Requirements

Establish comprehensive generics strategy:

- Consistent template naming conventions
- Covariance and contravariance handling
- Template inheritance patterns
- Bounded generics where appropriate

### 10.2 Investigation Questions

**Q10.1:** What template naming convention should we use? (`T`, `TData`, `TField`, etc.)

**Q10.2:** How do we handle covariant return types in PHP 7.4?

**Q10.3:** How do we handle contravariant parameter types in PHP 7.4?

**Q10.4:** What is the syntax for bounded generics? (`@template T of SomeClass`)

**Q10.5:** How do we propagate generics through inheritance chains?

**Q10.6:** When should we use `@template-covariant` vs regular `@template`?

**Q10.7:** How do we import types with `@phpstan-import-type`?

**Q10.8:** What are the limitations of PHP 7.4 generics via PHPDoc?

**Q10.9:** How do we document generic types for IDE support?

**Q10.10:** Are there any circular type dependencies we need to handle?

### 10.3 Investigation Status

- [ ] Q10.1 - Naming convention established
- [ ] Q10.2 - Covariant returns handled
- [ ] Q10.3 - Contravariant params handled
- [ ] Q10.4 - Bounded generics syntax documented
- [ ] Q10.5 - Inheritance propagation designed
- [ ] Q10.6 - Covariant template usage documented
- [ ] Q10.7 - Type import syntax documented
- [ ] Q10.8 - PHP 7.4 limitations documented
- [ ] Q10.9 - IDE documentation approach defined
- [ ] Q10.10 - Circular dependencies handled

---

## 11. Internal Utilities Typing

### 11.1 Requirements

Complete typing for internal utilities:

- Exception classes
- Helper functions
- Internal data structures (ArrayOptions, etc.)
- Any procedural code

### 11.2 Investigation Questions

**Q11.1:** What internal utility classes exist? List them.

**Q11.2:** What is the type structure of `ArrayOptions`?

**Q11.3:** Are there any procedural functions that need typing?

**Q11.4:** What exception classes exist and what is their hierarchy?

**Q11.5:** Are there any static helper methods that need typing?

**Q11.6:** What is the type for i18n/translation functions if any?

**Q11.7:** How do we type any reflection-based utilities?

**Q11.8:** Are there any array manipulation utilities needing generics?

**Q11.9:** What constant definitions exist and should they be typed?

**Q11.10:** Are there any autoloading or bootstrapping functions?

### 11.3 Investigation Status

- [ ] Q11.1 - Utility classes listed
- [ ] Q11.2 - ArrayOptions typed
- [ ] Q11.3 - Procedural functions identified
- [ ] Q11.4 - Exception hierarchy documented
- [ ] Q11.5 - Static helpers typed
- [ ] Q11.6 - i18n functions typed
- [ ] Q11.7 - Reflection utilities typed
- [ ] Q11.8 - Array utilities generified
- [ ] Q11.9 - Constants documented
- [ ] Q11.10 - Bootstrap code typed

---

## 12. Testing Type Coverage

### 12.1 Requirements

Ensure type coverage in test code:

- PHPUnit test classes typed
- Test fixtures and mocks typed
- Type assertions in tests
- CI validation of types

### 12.2 Investigation Questions

**Q12.1:** Should test code be included in static analysis? What level?

**Q12.2:** What PHPUnit-specific PHPStan/Psalm extensions should we use?

**Q12.3:** How do we type mock objects and test doubles?

**Q12.4:** Should test helper classes be fully typed?

**Q12.5:** How do we handle data providers with proper typing?

**Q12.6:** What is the proper type for `$this->assertXxx()` methods?

**Q12.7:** How do we type test fixtures in `supporting_classes/`?

**Q12.8:** Should we use `@phpstan-assert` for custom assertions?

**Q12.9:** How do we configure analysis to run separately on src/ vs tests/?

**Q12.10:** What CI workflow ensures type regression prevention?

### 12.3 Investigation Status

- [ ] Q12.1 - Test inclusion policy decided
- [ ] Q12.2 - PHPUnit extensions configured
- [ ] Q12.3 - Mock typing approach defined
- [ ] Q12.4 - Helper class typing policy set
- [ ] Q12.5 - Data provider typing solved
- [ ] Q12.6 - Assertion method typing understood
- [ ] Q12.7 - Fixture typing approach defined
- [ ] Q12.8 - Custom assertion typing evaluated
- [ ] Q12.9 - Separate analysis configuration designed
- [ ] Q12.10 - CI workflow defined

---

## Appendix A: Priority Order

After investigation, implement in this order:

1. **Phase 1: Foundation**
   - Current state assessment (Section 1)
   - PHPStan level 6 with zero errors
   - Psalm installation and initial run

2. **Phase 2: Core Types**
   - Base Field class generics
   - Base Validator class generics
   - Base Widget class generics

3. **Phase 3: Full Coverage**
   - All field subclasses
   - All validator subclasses
   - All widget subclasses

4. **Phase 4: Advanced**
   - Form and Meta typing
   - CSRF typing
   - Taint analysis

5. **Phase 5: Maximum**
   - PHPStan level 9
   - Psalm level 1
   - Full taint coverage

---

## Appendix B: Investigation Completion Criteria

The investigation phase is **COMPLETE** when:

1. All checkboxes in all sections are marked complete
2. Each question has a documented answer in the answers file
3. Any blocking issues are documented with proposed solutions
4. A prioritized implementation order is established

Output `<promise>INVESTIGATION_COMPLETE</promise>` when all criteria are met.
