<?php

declare(strict_types=1);

namespace WTForms\Fields\Support;

/**
 * A select field option: an immutable key-label pair.
 *
 * @implements PairInterface<string, string>
 */
final readonly class SelectFieldChoice implements PairInterface
{
  /** @use PairTrait<string, string> */
  use PairTrait;

  public function __construct(string $key, string $value)
  {
    $this->initPair($key, $value);
  }
}
