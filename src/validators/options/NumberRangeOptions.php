<?php

declare(strict_types=1);

namespace WTForms\Validators\Options;

/**
 * Options for the NumberRange validator.
 *
 * @phpstan-type NumberRangeOptionsArray array{min?:int|float|null, max?:int|float|null}
 * @extends ArrayOptions<NumberRangeOptionsArray>
 */
class NumberRangeOptions extends ArrayOptions
{
  /** @var int|float|null */
  public $min = null;

  /** @var int|float|null */
  public $max = null;

  protected function getDefaults(): array
  {
    return ['min' => null, 'max' => null];
  }
}
