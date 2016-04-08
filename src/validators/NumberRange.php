<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/27/2016
 * Time: 7:19 PM
 */

namespace WTForms\Validators;

use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Validates that a number is of a minimum and/or maximum value, inclusive.
 * This will work with any comparable number type, such as floats and
 * decimals, not just integers
 * @package WTForms\Validators
 */
class NumberRange extends Validator
{
  /**
   * @var float|int
   */
  public $min;

  /**
   * @var float|int
   */
  public $max;

  /**
   * NumberRange constructor.
   *
   * @param string $message Error message to raise in case of a validation error. // TODO: User interpolation
   * @param array  $other_options
   *
   * @internal                     param float|int $min The minimum required value of the number. If not provided,
   *                               minimum value will not be checked
   * @internal                     param float|int $max The maximum value of the number. If not provided, maximum
   *                               value will not be checked
   */
  public function __construct($message = "", array $other_options = ['min' => null, 'max' => null])
  {
    $this->min = $other_options['min'];
    $this->max = $other_options['max'];
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
    $data = $field->data;
    if (is_null($data) || (!is_null($this->min) && $data < $this->min) || (!is_null($this->max) && $data > $this->max)) {
      $message = $this->message;
      if ($message == "") {
        if (is_null($this->max)) {
          $message = "Number must be at least {$this->min}.";
        } elseif (is_null($this->min)) {
          $message = "Number must be at most {$this->max}.";
        } else {
          $message = "Number must be between {$this->min} and {$this->max}.";
        }
      }
      throw new ValidationError($message);
    }
  }
}