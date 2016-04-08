<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/22/2016
 * Time: 8:21 PM
 */

namespace WTForms\Validators;


use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Validates the length of a string
 * @package WTForms\Validators
 */
class Length extends Validator
{
  /**
   * @var int The minimum required length of the string
   */
  public $min;
  /**
   * @var int The maximum length of the string
   */
  public $max;


  /**
   * Validates the length of a string.
   *
   * @param string $message Error message to raise in case of a validation error. TODO: Implement user interpolation
   * @param array  $other_options
   */
  public function __construct($message = "", array $other_options = ['min' => -1, 'max' => -1])
  {
    $other_options = array_merge(['min' => -1, 'max' => -1], $other_options);
    assert(($other_options['min'] != -1 || $other_options['max'] != -1), "At least one of `min` or `max` must be specified");
    assert(($other_options['max'] == -1 || $other_options['min'] <= $other_options['max']), "`min` cannot be more than `max`");
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
    if ($field->data !== null) {
      $length = strlen($field->data);
    } else {
      $length = 0;
    }
    if ($length < $this->min || $this->max != -1 && $length > $this->max) {
      $message = $this->message;
      if ($message == "") {
        if ($this->max == -1) {
          if ($this->min > 1) {
            $message = "Field must be at least {$this->min} characters long.";
          } else {
            $message = "Field must be at least {$this->min} character long.";
          }
        } elseif ($this->min == -1) {
          if ($this->max > 1) {
            $message = "Field cannot be longer than {$this->max} characters long.";
          } else {
            $message = "Field cannot be longer than {$this->max} character long.";
          }
        } else {
          $message = "Field must be between {$this->min} and {$this->max} characters long.";
        }
        throw new ValidationError($message);
      }
    }
  }
}
