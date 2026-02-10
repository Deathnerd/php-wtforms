<?php

declare(strict_types=1);

namespace WTForms\Internal\Types;

/**
 * Default implementation for {@see HashMapInterface}.
 *
 * @template K
 * @template V
 *
 * @phpstan-require-implements HashmapInterface<K, V>
 */
trait HashMapTrait
{
  /** @var array<K, V> */
  private array $data = [];

  /**
   * @param array<K, V> $items
   */
  protected function initMap(array $items = []): void
  {
    $this->data = $items;
  }

  /**
   * @param K           $key
   * @param V|UnsetType $default
   *
   * @return V
   *
   * @throws \OutOfBoundsException if key not found and no default provided
   */
  #[\ReturnTypeWillChange]
  public function get($key, mixed $default = new UnsetType()): mixed
  {
    if (array_key_exists($key, $this->data)) {
      return $this->data[$key];
    }

    if ($default instanceof UnsetType) {
      throw new \OutOfBoundsException("Key '$key' not found");
    }

    return $default;
  }

  public function has(string|int $key): bool
  {
    return array_key_exists($key, $this->data);
  }

  /**
   * @param K $key
   * @param V $value
   */
  public function set(string|int $key, mixed $value): void
  {
    $this->data[$key] = $value;
  }

  /**
   * @param K $key
   */
  public function remove(string|int $key): void
  {
    unset($this->data[$key]);
  }

  /** @return array<K, V> */
  public function toArray(): array
  {
    return $this->data;
  }

  /** @return list<K> */
  public function keys(): array
  {
    return array_keys($this->data);
  }

  /** @return list<V> */
  public function values(): array
  {
    return array_values($this->data);
  }

  // ---------------------------------------------------------------------------
  // ArrayAccess
  // ---------------------------------------------------------------------------

  public function offsetExists(mixed $offset): bool
  {
    return $this->has($offset);
  }

  #[\ReturnTypeWillChange]
  public function offsetGet(mixed $offset): mixed
  {
    return $this->data[$offset] ?? null;
  }

  public function offsetSet(mixed $offset, mixed $value): void
  {
    if ($offset === null) {
      $this->data[] = $value;
    } else {
      $this->data[$offset] = $value;
    }
  }

  public function offsetUnset(mixed $offset): void
  {
    $this->remove($offset);
  }

  // ---------------------------------------------------------------------------
  // Countable + IteratorAggregate
  // ---------------------------------------------------------------------------

  public function count(): int
  {
    return count($this->data);
  }

  /** @return \Traversable<K, V> */
  public function getIterator(): \Traversable
  {
    return new \ArrayIterator($this->data);
  }
}
