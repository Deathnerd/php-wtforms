<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 11:26 PM
 */

namespace WTForms\Validators;

use WTForms\Exceptions\TypeError;
use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Core\Field;
use WTForms\Form;


/**
 * Compares the incoming data to a sequence of valid inputs
 * @package WTForms\Validators
 */
class AnyOf extends Validator
{
    /**
     * @var array
     */
    public $values;

    /**
     * @var callable
     */
    private $user_formatter;

    /**
     * AnyOf constructor.
     *
     * @param string $message       Error message to raise in case of a validation error
     * @param array  $options       Can pass in an array of values with the `'values'` key to use as part of validation
     *                              and also a `'formatter'` callable function to use when formatting an error message.
     *                              The formatter takes in one argument: an array of the values that were passed in
     *                              during validator construction
     *
     * @throws TypeError
     */
    public function __construct($message = "", array $options = ['formatter' => null])
    {
        if (!array_key_exists('values', $options)) {
            throw new TypeError("AnyOf validator expects an array of values!");
        }

        if (!is_array($options['values'])) {
            throw new TypeError("Values passed to AnyOf must be in the form of an array");
        }

        if (array_key_exists('formatter', $options)) {
            if (!is_null($options['formatter']) && !is_callable($options['formatter'])) {
                throw new TypeError("Formatter must be a callable; " . gettype($options['formatter']) . " found");
            }
            $this->user_formatter = $options['formatter'];
        } else {
            $this->user_formatter = null;
        }

        $this->values = $options['values'];
        $this->message = $message;
    }

    /**
     * @param Form   $form
     * @param Field  $field
     * @param string $message
     *
     * @throws ValidationError
     */
    public function __invoke(Form $form, Field $field, $message = "")
    {
        if (!in_array($field->data, $this->values)) {
            $message = $this->message;
            $value_string = $this->formatter($this->values);
            if ($message == "") {
                $message = "Invalid value, must be one of: $value_string.";
            } else {
                $message = sprintf($message, $value_string);
            }
            throw new ValidationError($message);
        }
    }

    /**
     * If a callable was passed into the ``$options['formatter']`` in the constructor, this
     * will call that to format the values to a string. Otherwise calls a default
     * formatter: ``implode``
     *
     * @param array $values The values to format
     *
     * @return string The formatted string
     */
    protected function formatter(array $values)
    {
        if (!is_null($this->user_formatter) && is_callable($this->user_formatter)) {
            return $this->user_formatter->__invoke($values);
        }

        return implode(", ", $values);
    }
}
