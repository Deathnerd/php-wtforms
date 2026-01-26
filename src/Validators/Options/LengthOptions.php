<?php

declare(strict_types=1);

namespace WTForms\Validators\Options;

use WTForms\Internal\Types\ArrayOptions;

/**
 * Options for the Length validator.
 *
 * @phpstan-type LengthOptionsArray array{min?:int, max?:int}
 *
 * @extends ArrayOptions<LengthOptionsArray>
 */
class LengthOptions extends ArrayOptions
{
  public int $min = -1;
  public int $max = -1;

  protected function getDefaults(): array
  {
    return ['min' => -1, 'max' => -1];
  }
}
