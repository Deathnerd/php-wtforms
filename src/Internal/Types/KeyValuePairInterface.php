<?php

declare(strict_types=1);

namespace WTForms\Internal\Types;

/**
 * An immutable two-element tuple with typed key and value.
 *
 * @template K of string
 * @template V
 */
interface KeyValuePairInterface
{
  /**
   * @return K
   */
  public function key(): string;

  /**
   * @return V
   */
  #[\ReturnTypeWillChange]
  public function value(): mixed;
}
