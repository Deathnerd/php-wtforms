<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 11:26 PM.
 */

namespace WTForms\Validators;

use WTForms\Exceptions\TypeError;
use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Core\Field;
use WTForms\Form;
use WTForms\Validators\Options\AnyOfOptions;

/**
 * Compares the incoming data to a sequence of valid inputs.
 *
 * @phpstan-import-type AnyOfOptionsArray from AnyOfOptions
 *
 * @template-extends Validator<AnyOfOptionsArray>
 */
class AnyOf extends Validator
{
  public array $values;

  /**
   * @var callable|null
   */
  private $user_formatter;

  /**
   * AnyOf constructor.
   *
   * @param string                         $message Error message to raise in case of a validation error
   * @param AnyOfOptionsArray|AnyOfOptions $options Can pass in an array of values with the `'values'` key to use as part of validation
   *                                                and also a `'formatter'` callable function to use when formatting an error message.
   *                                                The formatter takes in one argument: an array of the values that were passed in
   *                                                during validator construction
   *
   * @throws TypeError
   */
  public function __construct(string $message = '', $options = [])
  {
    parent::__construct($message, $options);
    if (!array_key_exists('values', $this->getOptions())) {
      throw new TypeError('AnyOf validator expects an array of values!');
    }

    if (!is_array($this->getOption('values'))) {
      throw new TypeError('Values passed to AnyOf must be in the form of an array');
    }

    if (array_key_exists('formatter', $options)) {
      if (!is_null($this->getOption('formatter')) && !is_callable($this->getOption('formatter'))) {
        throw new TypeError(sprintf('Formatter must be a callable; %s found', gettype($this->getOption('formatter'))));
      }
      $this->user_formatter = $this->getOption('formatter');
    } else {
      $this->user_formatter = null;
    }

    $this->values = $this->getOption('values');
  }

  /**
   * @return void
   *
   * @throws ValidationError
   */
  public function __invoke(Form $form, Field $field, string $message = '')
  {
    if (!in_array($field->data, $this->values)) {
      $message = $this->message;
      $value_string = $this->formatter($this->values);
      if ('' == $message) {
        $message = sprintf('Invalid value, must be one of: %s.', $value_string);
      } else {
        $message = sprintf($message, $value_string);
      }
      throw new ValidationError($message);
    }
  }

  /**
   * If a callable was passed into the ``$options['formatter']`` in the constructor, this
   * will call that to format the values to a string. Otherwise, calls a default
   * formatter: ``implode``.
   *
   * @param array $values The values to format
   *
   * @return string The formatted string
   */
  protected function formatter(array $values): string
  {
    if (!is_null($this->user_formatter)) {
      return ($this->user_formatter)($values);
    }

    return implode(', ', $values);
  }
}
