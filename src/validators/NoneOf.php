<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 11:56 PM
 */

namespace WTForms\Validators;


use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Compares the incoming data to a sequence of invalid objects
 * @package WTForms\Validators
 */
class NoneOf extends AnyOf
{
  /**
   * NoneOf constructor.
   *
   * @param array  $values                  A sequence of invalid inputs.
   * @param string $message                 Error message to raise in case of a validation error. //TODO: User
   *                                        interpolation
   */
  public function __construct(array $values, $message = "")
  {
    parent::__construct($values, $message);
  }

  /**
   * @inheritdoc
   */
  public function __invoke(Form $form, Field $field, $message = "")
  {
    if (in_array($field->data, $this->values)) {
      $message = $this->message;
      $values_string = $this->formatter($this->values);
      if ($message == "") {
        $message = "Invalid value, can't be any of: $values_string.";
      }
      throw new ValidationError($message);
    }
  }
}