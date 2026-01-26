<?php

declare(strict_types=1);

namespace WTForms\Widgets\HTML5;

use WTForms\Fields\Core\Field;
use WTForms\Widgets\Core\Input;

/**
 * Renders an input with the type "range".
 *
 * @extends Input<int|float|null>
 */
class RangeInput extends Input
{
  public ?string $step;

  /**
   * @param array{step?: string|null} $options
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
