<?php

declare(strict_types=1);

namespace WTForms\Widgets\HTML5;

use WTForms\Fields\Core\Field;
use WTForms\Fields\Options\FieldOptions;
use WTForms\Widgets\Core\Input;
use WTForms\Widgets\Options\NumberInputOptions as NumberInputOptionsClass;

/**
 * Renders an input with type "number".
 *
 * @phpstan-import-type FieldOptionsArray from FieldOptions
 * @phpstan-type NumberInputOptions array{step?: string|int|null, min?: string|int|null, max?: string|int|null}
 * @phpstan-type NumberInputDataType int|float|null
 *
 * @extends Input<NumberInputDataType>
 */
class NumberInput extends Input
{
  public string|int|null $min = null;
  public string|int|null $max = null;
  public string|int|null $step = null;

  /**
   * @phpstan-param NumberInputOptions & FieldOptionsArray $options
   */
  public function __construct(array|NumberInputOptionsClass $options = [])
  {
    if ($options instanceof NumberInputOptionsClass) {
      parent::__construct('number');
      $this->constructFromOptions($options);
      return;
    }
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
