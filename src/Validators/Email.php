<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 10:45 PM.
 */

namespace WTForms\Validators;

use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Validates an email address.
 *
 * @template-extends Validator<array{}>
 */
class Email extends Validator
{
  /**
   * MacAddress constructor.
   *
   * @param string $message error message to raise in case of a validation error
   */
  public function __construct(string $message = '', array $options = [])
  {
    parent::__construct($message, $options);
  }

  /**
   * @return void
   *
   * @throws ValidationError
   *
   * @phpstan-assert non-empty-string $field->data
   */
  public function __invoke(Form $form, Field $field, string $message = '')
  {
    $value = $field->data;
    $valid = false;
    if (!is_null($value)) {
      $valid = filter_var($value, FILTER_VALIDATE_EMAIL) && preg_match('/^.+@([^.@][^@]+)$/i', $value);
    }
    if (!$valid) {
      $message = $this->message;
      if ('' == $message) {
        $message = 'Invalid email address.';
      }
      throw new ValidationError($message);
    }
  }
}
