<?php

declare(strict_types=1);

namespace WTForms\Widgets\Options;

use WTForms\Internal\Types\ArrayOptions;

/**
 * Options for the NumberInput widget.
 *
 * @phpstan-type NumberInputOptionsArray array{step?: string|int|null, min?: string|int|null, max?: string|int|null}
 * @extends ArrayOptions<NumberInputOptionsArray>
 */
class NumberInputOptions extends ArrayOptions
{
  public string|int|null $step = null;
  public string|int|null $min = null;
  public string|int|null $max = null;

  public function hydrateObject(object $obj): object
  {
    $obj->step = $this->step;
    $obj->min = $this->min;
    $obj->max = $this->max;
    return $obj;
  }

  protected function getDefaults(): array
  {
    return ['step' => null, 'min' => null, 'max' => null];
  }
}
