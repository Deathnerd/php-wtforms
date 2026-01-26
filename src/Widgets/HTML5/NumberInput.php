<?php

declare(strict_types=1);

namespace WTForms\Widgets\HTML5;

use WTForms\Fields\Core\Field;
use WTForms\Widgets\Core\Input;

/**
 * Renders an input with type "number".
 *
 * @extends Input<int|float|null>
 */
class NumberInput extends Input
{
  public ?string $min;
  public ?string $max;
  public ?string $step;

  /**
   * @param array{step?: string|null, min?: string|null, max?: string|null} $options
   */
  public function __construct(array $options = [])
  {
    $options = array_merge(['step' => null, 'min' => null, 'max' => null], $options);
    $this->step = $options['step'];
    $this->min = $options['min'];
    $this->max = $options['max'];
    parent::__construct('number');
  }

  public function __invoke(Field $field, array $options = []): string
  {
    foreach (['step', 'min', 'max'] as $option) {
      if (!array_key_exists($option, $options) && $this->$option) {
        $options[$option] = $this->$option;
      }
    }

    return parent::__invoke($field, $options);
  }
}
