# Conformity Types — Design Document

## Vision

A PHP runtime library + PHPStan extension that bridges the gap between PHP's type system and the level of type/null safety expected in Kotlin or Java. Starts as a schema system for options-bag patterns, grows into a generics runtime for PHP 8.5+.

Analogous to Pydantic (core engine) consumed by Typer/FastAPI (domain libraries).

### Package Structure (future)

```
conformity/types          — Core: attributes, schema reflection, runtime validation
conformity/types-phpstan  — PHPStan adapter: reads schema, emits array shape types
conformity/forms          — Forms library (evolved php-wtforms), consumes types
```

### Current Phase: Dogfooding in php-wtforms

Everything lives under `src/Internal/` for now. Extract into packages once the options system is fully type-safe and proven.

---

## V0 Scope: Kill the `@phpstan-type` Duplication

### The Problem

Every options class manually maintains a `@phpstan-type` array shape that mirrors its public properties. This duplication exists across 20+ options classes and must stay in sync by hand.

### The Solution

Two attributes and a shared schema engine. The class hierarchy is the type declaration — no PHPDoc needed.

---

## Attributes

### `#[TypeSchema]` — Class Marker

Marks a class as participating in the computed type system. Applied once at the root of a hierarchy. All subclasses are automatically opted in.

```php
use Conformity\Types\Attributes\TypeSchema;

#[TypeSchema]
class FieldOptions extends ArrayOptions {
    // ...
}

// Automatically opted in — parent has #[TypeSchema]
class DateTimeFieldOptions extends FieldOptions {
    // ...
}
```

Opt out explicitly:

```php
#[TypeSchema(enabled: false)]
class InternalOptions extends FieldOptions {
    // Extension ignores this class
}
```

### `#[Property]` — Property Marker

Marks a property as part of the schema. Opt-in per property. A property without `#[Property]` is invisible to the schema.

```php
class FieldOptions extends ArrayOptions {
    #[Property]
    public array $validators = [];

    #[Property]
    public string $description = '';

    // No attribute = not part of the schema
    public string $internalState = '';
}

class DateTimeFieldOptions extends FieldOptions {
    #[Property]
    public string $format = '%Y-%m-%d %H:%i:%s';
}
```

Parameters (all optional):

| Parameter  | Type          | Default | Description                                                |
|------------|---------------|---------|------------------------------------------------------------|
| `optional` | `bool`        | `true`  | Whether the key is optional in the array shape.            |
| `name`     | `string|null` | `null`  | Override the array key if it differs from the property name.|

---

## Inheritance Model

The extension resolves `OptionsShape<T>` by walking the full class hierarchy, collecting every `#[Property]` from every ancestor that has (or inherits) `#[TypeSchema]`.

```php
#[TypeSchema]
class FieldOptions extends ArrayOptions {
    #[Property]
    public array $validators = [];

    #[Property]
    public string $description = '';
}

class DateTimeFieldOptions extends FieldOptions {
    #[Property]
    public string $format = '%Y-%m-%d %H:%i:%s';
}

class DateTimeLocalFieldOptions extends DateTimeFieldOptions {
    #[Property]
    public bool $withSeconds = false;
}
```

`OptionsShape<DateTimeLocalFieldOptions>` resolves to:

```
array{
    withSeconds?: bool,
    format?: string,
    validators?: list<Validator|callable>,
    description?: string,
}
```

No `@extends`, `@template`, or `@phpstan-import-type` annotations anywhere.

---

## Consumer Side: Constructor-Driven Discovery

The extension reads the constructor signature to discover which options schema applies. No annotations needed on consumer classes.

```php
class DateTimeField extends Field {
    public function __construct(array|DateTimeFieldOptions $options = []) { ... }
}
```

The extension sees `array|DateTimeFieldOptions`, checks if `DateTimeFieldOptions` participates in the type schema system, and resolves the `array` side of the union as `OptionsShape<DateTimeFieldOptions>`.

PHPStan then knows:

```php
new DateTimeField(['format' => '%Y-%m-%d']);         // valid
new DateTimeField(['fromat' => '%Y-%m-%d']);         // error: unknown key
```

### Subclassed consumers

```php
class MyDateTimeField extends DateTimeField {
    public function __construct(array|MyDateTimeFieldOptions $options = []) {
        parent::__construct($options);
    }
}
```

Same rule — reads the constructor, resolves against `MyDateTimeFieldOptions`. If the subclass doesn't override the constructor, it inherits the parent's, and the extension resolves against the parent's options type.

### No options class in signature

```php
public function __construct(array $options = []) { ... }
```

Extension doesn't activate. Graceful degradation.

---

## Schema Engine: Shared Core

The runtime reflection logic that both PHPStan and application code call into.

### `SchemaResolver`

```php
namespace Conformity\Types;

class SchemaResolver
{
    /** @var array<class-string, array<string, PropertyDefinition>> */
    private static array $cache = [];

    /**
     * Resolve the full schema for a class, walking its hierarchy.
     *
     * @param class-string $className
     * @return array<string, PropertyDefinition>
     */
    public static function resolve(string $className): array
    {
        return self::$cache[$className] ??= self::doResolve($className);
    }

    /**
     * Does this class participate in the type schema system?
     *
     * @param class-string $className
     */
    public static function isManaged(string $className): bool;

    /**
     * Validate an array against the resolved schema.
     *
     * @param class-string $className
     * @param array<string, mixed> $data
     * @throws TypeError
     */
    public static function validate(string $className, array $data): void;
}
```

### `PropertyDefinition`

```php
namespace Conformity\Types;

class PropertyDefinition
{
    public function __construct(
        public readonly string $name,
        public readonly string $key,
        public readonly string $type,
        public readonly \ReflectionType $reflectionType,
        public readonly bool $optional,
        public readonly bool $hasDefault,
        public readonly mixed $default,
        public readonly string $declaringClass,
    ) {}
}
```

### Integration with ArrayOptions

```php
// In ArrayOptions::hydrateProperties()
protected function hydrateProperties(): void
{
    if (!SchemaResolver::isManaged(static::class)) {
        $this->hydrateLegacy();
        return;
    }

    $schema = SchemaResolver::resolve(static::class);
    foreach ($schema as $key => $definition) {
        if (array_key_exists($key, $this->data)) {
            $this->{$definition->name} = $this->data[$key];
        }
    }
}
```

Backward compatible. Unmanaged classes use current behavior.

---

## PHPStan Extension

### `OptionsShape<T>` Type Resolver

```php
namespace Conformity\PHPStan;

use Conformity\Types\SchemaResolver;
use PHPStan\PhpDoc\TypeNodeResolverExtension;

class OptionsShapeTypeNodeResolver implements TypeNodeResolverExtension
{
    public function resolve(TypeNode $typeNode, NameScope $nameScope): ?Type
    {
        // 1. Match GenericTypeNode named "OptionsShape"
        // 2. Extract class name from generic argument
        // 3. Call SchemaResolver::resolve($className)
        // 4. Translate PropertyDefinition[] → ConstantArrayType
    }
}
```

### Constructor Parameter Type Narrowing

A `DynamicConstructorParameterExtension` (or similar) that intercepts constructor calls where the parameter type is `array|SomeOptionsClass`:

1. Check if `SomeOptionsClass` is managed via `SchemaResolver::isManaged()`
2. If yes, narrow the `array` side to `OptionsShape<SomeOptionsClass>`

### Registration

```neon
# conformity-types.neon
services:
    -
        class: Conformity\PHPStan\OptionsShapeTypeNodeResolver
        tags:
            - phpstan.phpDoc.typeNodeResolverExtension
    -
        class: Conformity\PHPStan\ConstructorParameterExtension
        tags:
            - phpstan.dynamicConstructorParameter
```

---

## Caching

Static in-memory cache in `SchemaResolver`. Schema for a class never changes within a process.

No file-based caching, no serialization, no invalidation. Sufficient for both runtime (request lifetime) and PHPStan (analysis process lifetime).

---

## Migration Strategy

Incremental. Each options class migrates independently. Old `@phpstan-type` annotations and new `#[Property]` attributes coexist.

1. Add `#[TypeSchema]` to `FieldOptions` (or `ArrayOptions`)
2. Pick one leaf: `DateTimeFieldOptions`. Add `#[Property]` to its properties. Delete its `@phpstan-type`.
3. Run PHPStan. Confirm resolution.
4. Repeat for next class. Work from leaves inward.
5. Once all subclasses migrated, add `#[Property]` to `FieldOptions` itself. Delete its `@phpstan-type`.
6. Repeat for `ValidatorOptions`, `WidgetOptions`, `FormOptions` hierarchies.

---

## Future Direction (not in v0)

- `#[TypeSchema(publicPropertiesByDefault: true)]` for opt-out mode
- Generic collections: `TypedList<T>`, `TypedMap<K, V>`
- `Pair<A, B>`, `HashMap<K, V>` with full PHPStan generics
- PHP 8.5+ pipe types integration
- Runtime type checking mirroring PHPStan's static checks
- Full generics runtime for PHP