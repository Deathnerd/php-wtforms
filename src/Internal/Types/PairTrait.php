<?php

declare(strict_types=1);

namespace WTForms\Internal\Types;

/**
 * Default implementation for {@see KeyValuePairInterface}.
 *
 * @template K of string
 * @template V
 */
trait PairTrait
{
  public readonly string $pairKey;
  public readonly mixed $pairValue;

  /**
   * @param K $key
   * @param V $value
   */
  protected function initPair(string $key, mixed $value): void
  {
    $this->pairKey = $key;
    $this->pairValue = $value;
  }

  /**
   * @return K
   */
  public function key(): string
  {
    return $this->pairKey;
  }

  /**
   * @return V
   */
  #[\ReturnTypeWillChange]
  public function value(): mixed
  {
    return $this->pairValue;
  }
}
