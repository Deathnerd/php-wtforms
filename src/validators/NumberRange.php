<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/27/2016
 * Time: 7:19 PM
 */

namespace Deathnerd\WTForms\Validators;

use Deathnerd\WTForms\BaseForm;
use Deathnerd\WTForms\Fields\Field;

/**
 * Validates that a number is of a minimum and/or maximum value, inclusive.
 * This will work with any comparable number type, such as floats and
 * decimals, not just integers
 * @package Deathnerd\WTForms\Validators
 */
class NumberRange extends Validator
{
    /**
     * @var float|int
     */
    public $min;

    /**
     * @var float|int
     */
    public $max;

    /**
     * NumberRange constructor.
     * @param integer|float $min The minimum required value of the number. If not provided,
     * minimum value will not be checked
     * @param integer|float $max The maximum value of the number. If not provided, maximum
     * value will not be checked
     * @param string $message Error message to raise in case of a validation error. // TODO: User interpolation
     */
    public function __construct($min = null, $max = null, $message = "")
    {
        $this->min = $min;
        $this->max = $max;
        $this->message = $message;
    }

    /**
     * @param BaseForm $form
     * @param Field $field
     * @throws ValidationError
     */
    public function __invoke(BaseForm $form, Field $field)
    {
        $data = $field->data;
        if (is_null($data) || (!is_null($this->min) && $data < $this->min) || (!is_null($this->max) && $data > $this->max)) {
            $message = $this->message;
            if ($message == "") {
                if (is_null($this->max)) {
                    $message = sprintf($field->gettext('Number must be at least %s.'), $this->min);
                } elseif (is_null($this->min)) {
                    $message = sprintf($field->gettext('Number must be at most %s.'), $this->max);
                } else {
                    $message = sprintf($field->gettext('Number must be between %s and %s.'), $this->min, $this->max);
                }
            }
            throw new ValidationError($message);
        }
    }
}