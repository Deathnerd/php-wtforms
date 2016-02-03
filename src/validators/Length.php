<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/22/2016
 * Time: 8:21 PM
 */

namespace Deathnerd\WTForms\Validators;


use Deathnerd\WTForms\Fields\Core\Field;

/**
 * Validates the length of a string
 * @package Deathnerd\WTForms\Validators
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
     * @param int $min The minimum required length of the string. If not provided, minimum length will not be checked
     * @param int $max The maximum length of the string. If not provided, maximum length will not be checked
     * @param string $message Error message to raise in case of a validation error. TODO: Implement user interpolation
     */
    public function __construct($min = -1, $max = -1, $message = "")
    {
        assert(($min != -1 || $max != -1), "At least one of `min` or `max` must be specified");
        assert(($max == -1 || $min <= $max), "`min` cannot be more than `max`");
        $this->min = $min;
        $this->max = $max;
        $this->message = $message;
    }

    public function __invoke($form, Field $field)
    {
        if (!is_null($field->data)) {
            $length = count($field->data);
        } else {
            $length = 0;
        }
        if ($length < $this->min || $this->max != -1 && $length > $this->max) {
            $message = $this->message;
            if ($message == "") {
                if ($this->max == -1) {
                    $message = sprintf($field->ngettext("Field must be at least %d character long", "Field must be at least %d characters long", $this->min), $this->min);
                } elseif ($this->min == -1) {
                    $message = sprintf($field->ngettext("Field cannot be longer than %d character.", "Field cannot be longer than %d characters.", $this->max), $this->max);
                } else {
                    $message = sprintf($field->gettext("Field must be between %d and %d characters long."), $this->min, $this->max);
                }
                throw new ValidationError($message);
            }
        }
    }
}
