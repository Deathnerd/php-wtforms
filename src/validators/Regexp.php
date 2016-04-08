<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 10:09 PM
 */

namespace WTForms\Validators;

use WTForms\Fields\Core\Field;
use WTForms\Form;


/**
 * Validates the field against a user-provided regular expression
 * @package WTForms\Validators
 */
class Regexp extends Validator
{
  /**
   * @var string
   */
  public $regex;

  /**
   * Assertion check is done here for a valid regular expression
   *
   * @param string $message Error message to raise in case of a validation error
   * @param array  $other_options
   *
   * @internal param string $regex The regular expression string to use.
   */
  public function __construct($message = "", array $other_options = ['regex' => ''])
  {
    assert(filter_var($other_options['regex'], FILTER_VALIDATE_REGEXP), "Invalid regular expression passed to Regexp");
    $this->regex = $other_options['regex'];
    $this->message = $message;
  }

  /**
   * @param Form   $form
   * @param Field  $field
   * @param string $message
   *
   * @return mixed The first match of the regular expression if one was found
   * @throws ValidationError
   */
  function __invoke(Form $form, Field $field, $message = "")
  {
    $match = preg_match($this->regex, $field->data ?: '', $matches);
    if (!$match) {
      if ($message == "") {
        if ($this->message == "") {
          $message = "Invalid Input.";
        } else {
          $message = $this->message;
        }
      }
      throw new ValidationError($message);
    }

    return $matches[0];
  }
}