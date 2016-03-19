<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 2:11 PM
 */

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Renders the individual option from a select field.
 *
 * This is just a convenience for various custom rendering situations, and an
 * option by itself does not constitute an entire field.
 *
 * @package WTForms\Widgets\Core
 */
class Option
{
  public function __invoke(Field $field, array $options = [])
  {
    return Select::renderOption($field->value, $field->label->text, $field->checked, $options);
  }
}
