<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 11:56 PM.
 */

namespace WTForms\Validators;

use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Core\Field;
use WTForms\Form;
use WTForms\Validators\Options\AnyOfOptions;
use WTForms\Validators\Options\ArrayOptions;

/**
 * Compares the incoming data to a sequence of invalid objects.
 *
 * @phpstan-import-type AnyOfOptionsArray from AnyOfOptions
 */
class NoneOf extends AnyOf
{
  /**
   * NoneOf Constructor.
   *
   * @param string                         $message Error message to raise in case of a validation error
   * @param AnyOfOptionsArray|AnyOfOptions $options
   */
  public function __construct(string $message = '', $options = [])
  {
    if ($options instanceof ArrayOptions) {
      $options = $options->toArray();
    }
    if (!isset($options['values'])) {
      $options['values'] = [];
    }
    parent::__construct($message, $options);
  }

  public function __invoke(Form $form, Field $field, string $message = '')
  {
    if (in_array($field->data, $this->values)) {
      $message = $this->message;
      $values_string = $this->formatter($this->values);
      if ('' == $message) {
        $message = "Invalid value, can't be any of: $values_string.";
      } else {
        $message = sprintf($message, $values_string);
      }
      throw new ValidationError($message);
    }
  }
}
