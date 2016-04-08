<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 11:26 PM
 */

namespace WTForms\Validators;

use WTForms\Fields\Core\Field;
use WTForms\Form;


/**
 * Compares the incoming data to a sequence of valid inputs
 * @package WTForms\Validators
 */
class AnyOf extends Validator
{
  /**
   * @var array
   */
  public $values;

  /**
   * AnyOf constructor.
   *
   * @param string $message                           Error message to raise in case of a validation error. TODO: User
   *                                                  interpolation
   * @param array  $other_options
   *
   */
  public function __construct($message = "", array $other_options = ['values' => []])
  {
    $this->values = $other_options['values'];
    $this->message = $message;
  }

  /**
   * @param Form   $form
   * @param Field  $field
   * @param string $message
   *
   * @throws ValidationError
   */
  public function __invoke(Form $form, Field $field, $message = "")
  {
    if (!in_array($field->data, $this->values)) {
      $message = $this->message;
      $value_string = $this->formatter($this->values);
      if ($message == "") {
        $message = "Invalid value, must be one of: $value_string.";
      } else {
        $message = sprintf($message, $value_string);
      }
      throw new ValidationError($message);
    }
  }

  /**
   * If a callable was passed as ``$values_formatter`` in the constructor, this
   * will call that to format the values to a string. Otherwise calls a default
   * formatter: ``implode``
   *
   * @param array $values The values to format
   *
   * @return string The formatted string
   */
  protected function formatter(array $values)
  {
    return implode(", ", $values);
  }
}