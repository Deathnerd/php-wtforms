<?php

declare(strict_types=1);

namespace WTForms\Widgets\Options;

use WTForms\Internal\Types\ArrayOptions;

/**
 * Options for the RangeInput widget.
 *
 * @phpstan-type RangeInputOptionsArray array{step?: string|int|null}
 * @extends ArrayOptions<RangeInputOptionsArray>
 */
class RangeInputOptions extends ArrayOptions
{
  public string|int|null $step = null;

  public function hydrateObject(object $obj): object
  {
    $obj->step = $this->step;
    return $obj;
  }

  protected function getDefaults(): array
  {
    return ['step' => null];
  }
}
