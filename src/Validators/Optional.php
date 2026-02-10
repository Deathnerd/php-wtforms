<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/27/2016
 * Time: 7:33 PM.
 */

namespace WTForms\Validators;

use WTForms\Exceptions\StopValidation;
use WTForms\Fields\Core\Field;
use WTForms\Form;
use WTForms\Validators\Options\OptionalOptions;

/**
 * Allows empty input and stops the validation chain from continuing.
 *
 * If input is empty, also removes prior errors (such as processing errors)
 * from the field
 */
class Optional extends Validator
{
  public array $field_flags = ['optional'];

  /**
   * @var callable
   */
  public $string_check;

  /**
   * @var bool
   */
  public $strip_whitespace;

  /**
   * Optional constructor.
   *
   * @param OptionalOptions $options
   */
  public function __construct(string $message = '', OptionalOptions $options = new OptionalOptions())
  {
    parent::__construct($message, $options);
  }

  /**
   * @throws StopValidation
   */
  public function __invoke(Form $form, Field $field, string $message = '')
  {
    if (is_null($field->raw_data)
        || empty($field->raw_data)
        || (is_string($field->raw_data[0]) && '' == $this->stringCheck($field->raw_data[0]))
    ) {
      $field->errors = [];
      throw new StopValidation();
    }
  }

  private function stringCheck(string $string): string
  {
    if ($this->strip_whitespace) {
      return trim($string);
    }

    return $string;
  }
}
