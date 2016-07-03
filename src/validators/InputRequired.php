<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/27/2016
 * Time: 8:04 PM
 */

namespace WTForms\Validators;

use WTForms\Exceptions\StopValidation;
use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Validates that input was provided for this field.
 *
 * Note there is a distinction between this and {@link DataRequired} in that
 * {@link InputRequired} looks that form-input data was provided, and {@link DataRequired}
 * looks at the post-coercion data
 *
 * @package WTForms\Validators
 */
class InputRequired extends Validator
{
    /**
     * @var array
     */
    public $field_flags = ['required'];

    /**
     * InputRequired constructor.
     *
     * @param string $message Message to raise if validation fails
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
     * @throws StopValidation
     */
    public function __invoke(Form $form, Field $field = null, $message = "")
    {
        if (is_null($field->raw_data) || empty($field->raw_data) ||
            (!empty($field->raw_data) && !$field->raw_data[0])
        ) {
            if ($this->message == "") {
                $message = "This field is required.";
            } else {
                $message = $this->message;
            }
            $field->errors = [];
            throw new StopValidation($message);
        }
    }
}
