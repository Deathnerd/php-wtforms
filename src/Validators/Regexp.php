<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 10:09 PM.
 */

namespace WTForms\Validators;

use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Core\Field;
use WTForms\Form;
use WTForms\Internal\Types\ArrayOptions;
use WTForms\Validators\Options\RegexpOptions;

/**
 * Validates the field against a user-provided regular expression.
 *
 * @phpstan-import-type RegexpOptionsArray from RegexpOptions
 *
 * @template-extends Validator<RegexpOptionsArray>
 */
class Regexp extends Validator
{
  /**
   * @var non-empty-string
   */
  public string $regex;

  /**
   * Assertion check is done here for a valid regular expression.
   *
   * @param string                           $message Error message to raise in case of a validation error
   * @param RegexpOptionsArray|RegexpOptions $options
   */
  public function __construct(string $message = '', array|RegexpOptions $options = new RegexpOptions())
  {
    if ($options instanceof RegexpOptions) {
      parent::__construct($message, $options);
      return;
    }
    assert(false !== preg_match($options['regex'], ''), 'Invalid regular expression passed to Regexp');
    parent::__construct($message, $options);
    $this->regex = $options['regex'];
  }

  /**
   * @return string The first match of the regular expression if one was found
   *
   * @throws ValidationError
   *
   * @phpstan-assert string $field->data
   */
  public function __invoke(Form $form, Field $field, string $message = ''): string
  {
    $match = preg_match($this->regex, $field->data ?: '', $matches);
    if (!$match) {
      if ('' == $message) {
        if ('' == $this->message) {
          $message = 'Invalid Input.';
        } else {
          $message = $this->message;
        }
      }
      throw new ValidationError($message);
    }

    return $matches[0];
  }
}
