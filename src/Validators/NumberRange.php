<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/27/2016
 * Time: 7:19 PM.
 */

namespace WTForms\Validators;

use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Core\Field;
use WTForms\Form;
use WTForms\Validators\Options\ArrayOptions;
use WTForms\Validators\Options\NumberRangeOptions;

/**
 * Validates that a number is of a minimum and/or maximum value, inclusive.
 * This will work with any comparable number type, such as floats and
 * decimals, not just integers.
 *
 * @phpstan-import-type NumberRangeOptionsArray from NumberRangeOptions
 *
 * @template-extends Validator<NumberRangeOptionsArray>
 */
class NumberRange extends Validator
{
  /**
   * @var float|int|null
   */
  public $min;

  /**
   * @var float|int|null
   */
  public $max;

  /**
   * NumberRange constructor.
   *
   * @param string                                     $message error message to raise in case of a validation error
   * @param NumberRangeOptionsArray|NumberRangeOptions $options
   */
  public function __construct(string $message = '', $options = [])
  {
    if ($options instanceof ArrayOptions) {
      $options = $options->toArray();
    }
    $options = array_merge(['min' => null, 'max' => null], $options);
    parent::__construct($message, $options);
    $this->min = $options['min'];
    $this->max = $options['max'];
  }

  /**
   * @return void
   *
   * @throws ValidationError
   *
   * @phpstan-assert !null $field->data
   */
  public function __invoke(Form $form, Field $field, string $message = '')
  {
    $data = $field->data;
    if (is_null($data)
        || (!is_null($this->min) && $data < $this->min)
        || (!is_null($this->max) && $data > $this->max)
    ) {
      $message = $this->message;
      if ('' == $message) {
        if (is_null($this->max)) {
          $message = sprintf('Number must be at least %d.', $this->min);
        } elseif (is_null($this->min)) {
          $message = sprintf('Number must be at most %d.', $this->max);
        } else {
          $message = sprintf('Number must be between %d and %d.', $this->min, $this->max);
        }
      } else {
        $message = vsprintf_named($message, ['min' => $this->min, 'max' => $this->max]);
      }
      throw new ValidationError($message);
    }
  }
}
