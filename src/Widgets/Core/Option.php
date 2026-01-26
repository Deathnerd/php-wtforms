<?php

declare(strict_types=1);

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Renders the individual option from a select field.
 *
 * This is just a convenience for various custom rendering situations, and an
 * option by itself does not constitute an entire field.
 *
 * @extends Widget<string|int>
 */
class Option extends Widget
{
  public function __invoke(Field $field, array $options = []): string
  {
    return Select::renderOption($field->value, $field->label->text, $field->checked, $options);
  }
}
