<?php

declare(strict_types=1);

namespace WTForms\Widgets\HTML5;

use WTForms\Fields\Core\Field;
use WTForms\Fields\Options\FieldOptions;
use WTForms\Widgets\Core\Input;

/**
 * Renders an input with the type "range".
 *
 * @phpstan-import-type FieldOptionsArray from FieldOptions
 * @phpstan-type RangeInputOptions array{step?: string|int|null}
 * @phpstan-type RangeInputDataType int|float|null
 * @extends Input<RangeInputDataType>
 */
class RangeInput extends Input
{
  public string|int|null $step;

  /**
   * @phpstan-param RangeInputOptions & FieldOptionsArray $options
   */
  public function __construct(array $options = [])
  {
    $options = array_merge(['step' => null], $options);
    $this->step = $options['step'];
    parent::__construct('range');
  }

  public function __invoke(Field $field, array $options = []): string
  {
    return parent::__invoke($field, array_merge(['step' => $this->step], $options));
  }
}
