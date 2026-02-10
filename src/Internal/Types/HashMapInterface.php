<?php

declare(strict_types=1);

namespace WTForms\Internal\Types;

use Countable;
use IteratorAggregate;

/**
 * A mutable, type-safe key-value map.
 *
 * @template K of string
 * @template V
 *
 * @extends \ArrayAccess<K, V>
 * @extends \IteratorAggregate<K, V>
 */
interface HashMapInterface extends \ArrayAccess, Countable, IteratorAggregate
{
  /**
   * @param K $key
   */
  public function has(string $key): bool;

  /**
   * @param K           $key
   * @param V|UnsetType $default
   *
   * @return V
   *
   * @throws \OutOfBoundsException if key not found and no default provided
   */
  #[\ReturnTypeWillChange]
  public function get(string $key, mixed $default = new UnsetType()): mixed;

  /**
   * @param K $key
   * @param V $value
   */
  public function set(string $key, mixed $value): void;

  /**
   * @param K $key
   */
  public function remove(string $key): void;

  /**
   * @return array<K, V>
   */
  public function toArray(): array;

  /**
   * @return list<K>
   */
  public function keys(): array;

  /**
   * @return list<V>
   */
  public function values(): array;
}
