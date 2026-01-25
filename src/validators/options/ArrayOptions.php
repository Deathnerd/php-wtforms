<?php

declare(strict_types=1);

namespace WTForms\Validators\Options;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Base class for type-safe options that can be used interchangeably with arrays.
 *
 * Concrete implementations should define typed public properties that map to array keys.
 * The class implements all array interfaces so it can be used anywhere an array would be.
 *
 * @template TShape of array<string, mixed>
 * @implements ArrayAccess<key-of<TShape>, value-of<TShape>>
 * @implements IteratorAggregate<key-of<TShape>, value-of<TShape>>
 * @phpstan-consistent-constructor
 */
abstract class ArrayOptions implements ArrayAccess, Countable, IteratorAggregate
{
  /**
   * Internal array storage
   * @var TShape
   */
  protected array $data = [];

  /**
   * @param TShape $data
   */
  public function __construct(array $data = [])
  {
    $this->data = array_merge($this->getDefaults(), $data);
    $this->hydrateProperties();
  }

  /**
   * Get default values for this options class.
   * Override in subclasses to provide defaults.
   *
   * @return array<string, mixed>
   */
  protected function getDefaults(): array
  {
    return [];
  }

  /**
   * Hydrate public properties from the internal data array.
   */
  protected function hydrateProperties(): void
  {
    foreach ($this->data as $key => $value) {
      if (property_exists($this, $key)) {
        $this->$key = $value;
      }
    }
  }

  /**
   * Sync a property value back to the internal array.
   * Call this after modifying a property directly.
   */
  protected function syncToArray(string $property): void
  {
    if (property_exists($this, $property)) {
      $this->data[$property] = $this->$property;
    }
  }

  /**
   * Convert to a plain array.
   *
   * @return TShape
   */
  public function toArray(): array
  {
    // Sync all public properties to array before returning
    $reflection = new \ReflectionClass($this);
    foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
      $name = $prop->getName();
      if ($name !== 'data') {
        $this->data[$name] = $this->$name;
      }
    }
    return $this->data;
  }

  /**
   * Create from an array or return the ArrayOptions instance as-is.
   *
   * @param TShape|static $options
   * @return static
   */
  public static function from($options)
  {
    if ($options instanceof static) {
      return $options;
    }
    return new static($options);
  }

  // -------------------------------------------------------------------------
  // ArrayAccess implementation
  // -------------------------------------------------------------------------

  /**
   * @param string $offset
   */
  public function offsetExists($offset): bool
  {
    return array_key_exists($offset, $this->data) || property_exists($this, $offset);
  }

  /**
   * @param string $offset
   * @return mixed
   */
  public function offsetGet($offset)
  {
    // Prefer property value if it exists (it may have been modified directly)
    if (property_exists($this, $offset) && $offset !== 'data') {
      return $this->$offset;
    }
    return $this->data[$offset] ?? null;
  }

  /**
   * @param string $offset
   * @param mixed $value
   */
  public function offsetSet($offset, $value): void
  {
    $this->data[$offset] = $value;
    if (property_exists($this, $offset) && $offset !== 'data') {
      $this->$offset = $value;
    }
  }

  /**
   * @param string $offset
   */
  public function offsetUnset($offset): void
  {
    unset($this->data[$offset]);
    if (property_exists($this, $offset) && $offset !== 'data') {
      $this->$offset = null;
    }
  }

  // -------------------------------------------------------------------------
  // Countable implementation
  // -------------------------------------------------------------------------

  public function count(): int
  {
    return count($this->data);
  }

  // -------------------------------------------------------------------------
  // IteratorAggregate implementation
  // -------------------------------------------------------------------------

  public function getIterator(): Traversable
  {
    // Sync properties before iterating
    $this->toArray();
    return new ArrayIterator($this->data);
  }

  // -------------------------------------------------------------------------
  // Magic methods for property-style access to array keys
  // -------------------------------------------------------------------------

  /**
   * @param string $name
   * @return mixed
   */
  public function __get(string $name)
  {
    return $this->data[$name] ?? null;
  }

  /**
   * @param string $name
   * @param mixed $value
   */
  public function __set(string $name, $value): void
  {
    $this->data[$name] = $value;
  }

  public function __isset(string $name): bool
  {
    return isset($this->data[$name]);
  }

  public function __unset(string $name): void
  {
    unset($this->data[$name]);
  }
}
