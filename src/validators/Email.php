<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 10:45 PM
 */

namespace WTForms\Validators;


use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Validates an Address
 * @package WTForms\Validators
 */
class Email extends Validator
{
    /**
     * MacAddress constructor.
     *
     * @param string $message Error message to raise in case of a validation error.
     * @param array  $options
     */
    public function __construct($message = "", array $options = [])
    {
        $this->message = $message;
    }

    /**
     * @param Form   $form
     * @param Field  $field
     * @param string $message
     *
     * @return mixed
     * @throws ValidationError
     */
    public function __invoke(Form $form, Field $field, $message = "")
    {
        $value = $field->data;
        $valid = false;
        if (!is_null($value)) {
            $valid = filter_var($value, FILTER_VALIDATE_EMAIL) && preg_match('/^.+@([^.@][^@]+)$/i', $value);
        }
        if (!$valid) {
            $message = $this->message;
            if ($message == "") {
                $message = "Invalid email address.";
            }
            throw new ValidationError($message);
        }
    }
}
