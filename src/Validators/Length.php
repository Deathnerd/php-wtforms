<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/22/2016
 * Time: 8:21 PM.
 */

namespace WTForms\Validators;

use WTForms\Exceptions\TypeError;
use WTForms\Exceptions\ValidationError;
use WTForms\Exceptions\ValueError;
use WTForms\Fields\Core\Field;
use WTForms\Form;
use WTForms\Internal\Types\ArrayOptions;
use WTForms\Validators\Options\LengthOptions;

/**
 * Validates the length of a string.
 *
 * @phpstan-import-type LengthOptionsArray from LengthOptions
 *
 * @template-extends Validator<LengthOptionsArray>
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
   * @param string                           $message error message to raise in case of a validation error
   * @param LengthOptionsArray|LengthOptions $options
   *
   * @throws TypeError
   * @throws ValueError
   */
  public function __construct(string $message = '', $options = [])
  {
    if ($options instanceof ArrayOptions) {
      $options = $options->toArray();
    }
    $options = array_merge(['min' => -1, 'max' => -1], $options);
    if (!(-1 != $options['min'] || -1 != $options['max'])) {
      throw new TypeError('At least one of min or max must be specified');
    }
    if (!(-1 == $options['max'] || $options['min'] <= $options['max'])) {
      throw new ValueError('min cannot be more than max');
    }
    parent::__construct($message, $options);
    $this->min = $options['min'];
    $this->max = $options['max'];
  }

  /**
   * @throws ValidationError
   */
  public function __invoke(Form $form, Field $field, string $message = '')
  {
    if (null !== $field->data) {
      $length = strlen($field->data);
    } else {
      $length = 0;
    }
    if ($length < $this->min || -1 != $this->max && $length > $this->max) {
      $message = $this->message;
      if ('' == $message) {
        if (-1 == $this->max) {
          if ($this->min > 1) {
            $message = sprintf('Field must be at least %d characters long.', $this->min);
          } else {
            $message = sprintf('Field must be at least %d character long.', $this->min);
          }
        } elseif (-1 == $this->min) {
          if ($this->max > 1) {
            $message = sprintf('Field cannot be longer than %d characters long.', $this->max);
          } else {
            $message = sprintf('Field cannot be longer than %d character long.', $this->max);
          }
        } else {
          $message = sprintf('Field must be between %d and %d characters long.', $this->min, $this->max);
        }
      } else {
        $message = vsprintf_named($message, ['min' => $this->min, 'max' => $this->max]);
      }
      throw new ValidationError($message);
    }
  }
}
