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

  /**
   * @throws \WTForms\Exceptions\TypeError
   * @throws \WTForms\Exceptions\ValueError
   */
  public function validate(): void
  {
    if ($this->min === -1 && $this->max === -1) {
      throw new \WTForms\Exceptions\TypeError('At least one of min or max must be specified');
    }
    if ($this->max !== -1 && $this->min > $this->max) {
      throw new \WTForms\Exceptions\ValueError('min cannot be more than max');
    }
  }

  /**
   * @phpstan-param \WTForms\Validators\Length $obj
   * @return \WTForms\Validators\Length
   */
  public function hydrateObject(object $obj): object
  {
    $obj->min = $this->min;
    $obj->max = $this->max;
    return $obj;
  }

  protected function getDefaults(): array
  {
    return ['min' => -1, 'max' => -1];
  }
}
