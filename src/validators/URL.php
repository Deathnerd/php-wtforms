<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 10:58 PM
 */

namespace WTForms\Validators;

use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Simple url validation using PHP's filter_var.
 * @package WTForms\Validators
 */
class URL extends Validator
{
  /**
   * URL constructor.
   *
   * @param string $message Error message to raise in case of a validation error
   * @param array  $other_options
   */
  public function __construct($message = "", array $other_options = [])
  {
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
    if (!is_null($field->data)) {
      $valid = filter_var($field->data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) || filter_var($field->data, FILTER_VALIDATE_URL);
      if (!$valid) {
        throw new ValidationError($this->message ?: "Invalid URL");
      }
    }
  }
}