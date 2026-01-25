<?php

declare(strict_types=1);

namespace WTForms\Validators\Options;

/**
 * Options for the AnyOf validator.
 *
 * @phpstan-type AnyOfOptionsArray array{values?:array, formatter?:callable|null}
 * @extends ArrayOptions<AnyOfOptionsArray>
 */
class AnyOfOptions extends ArrayOptions
{
  /** @var array */
  public array $values = [];

  /** @var callable|null */
  public $formatter = null;

  protected function getDefaults(): array
  {
    return ['values' => [], 'formatter' => null];
  }
}
