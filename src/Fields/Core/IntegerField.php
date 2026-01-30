<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 1:24 PM.
 */

namespace WTForms\Fields\Core;

use WTForms\Exceptions\ValueError;
use WTForms\Fields\Options\FieldOptions;
use WTForms\Widgets\Core\TextInput;

/**
 * A text field, except all input is coerced to an integer. Erroneous input
 * is ignored and will not be accepted as a value.
 *
 * @phpstan-type IntegerFieldDataType int|null
 * @extends Field<IntegerFieldDataType>
 *
 * @phpstan-import-type FieldOptionsArray from FieldOptions
 */
class IntegerField extends Field
{
  /**
   * IntegerField constructor.
   *
   * @phpstan-param FieldOptionsArray $options
   *
   * @phpstan-param array<string, mixed> $options
   */
  public function __construct(array $options = [])
  {
    $options = array_merge(['widget' => new TextInput()], $options);
    parent::__construct($options);
  }

  /**
   * Process data received over the wire from a form.
   *
   * @param array<int, string> $valuelist A list of strings to process
   *
   * @throws ValueError When the value is not a valid integer
   */
  public function processFormData(array $valuelist): void
  {
    if ($valuelist) {
      if (is_numeric($valuelist[0])) {
        $this->data = intval($valuelist[0]);
      } else {
        $this->data = null;
        throw new ValueError('Not a valid integer value');
      }
    }
  }
}
