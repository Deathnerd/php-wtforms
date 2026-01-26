<?php

declare(strict_types=1);

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Render a checkbox.
 *
 * The ``checked`` HTML attribute is set if the field's data is a non-false value.
 *
 * @extends Input<bool>
 */
class CheckboxInput extends Input
{
  /**
   * CheckboxInput constructor.
   */
  public function __construct()
  {
    parent::__construct('checkbox');
  }

  public function __invoke(Field $field, array $options = []): string
  {
    if ($field->checked or $field->data) {
      $options['checked'] = true;
    }

    return parent::__invoke($field, $options);
  }
}
