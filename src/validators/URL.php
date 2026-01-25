<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 10:58 PM
 */

namespace WTForms\Validators;

use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Simple url validation using PHP's filter_var.
 * @template-extends Validator<array{}>
 */
class URL extends Validator
{
  /**
   * URL constructor.
   *
   * @param string $message Error message to raise in case of a validation error
   * @param array  $options
   */
  public function __construct(string $message = "", array $options = [])
  {
    parent::__construct($message, $options);
  }

  /**
   * @param Form   $form
   * @param Field  $field
   * @param string $message
   *
   * @return void
   *
   * @throws ValidationError
   * @phpstan-assert non-empty-string $field->data
   */
  public function __invoke(Form $form, Field $field, string $message = "")
  {
    if (!is_null($field->data)) {
      $valid = filter_var($field->data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)
          || filter_var($field->data, FILTER_VALIDATE_URL);
      if (!$valid) {
        throw new ValidationError($this->message ?: "Invalid URL");
      }
    }
  }
}
