<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/27/2016
 * Time: 8:04 PM
 */

namespace Deathnerd\WTForms\Validators;

use Deathnerd\WTForms\BaseForm;
use Deathnerd\WTForms\Fields\Core\Field;

/**
 * Validates that input was provided for this field.
 *
 * Note there is a distinction between this and {@link DataRequired} in that
 * {@link InputRequired} looks that form-input data was provided, and {@link DataRequired}
 * looks at the post-coercion data
 *
 * @package Deathnerd\WTForms\Validators
 */
class InputRequired extends Validator
{
    /**
     * @var array
     */
    public $field_flags = ['required'];

    /**
     * InputRequired constructor.
     * @param string $message Message to raise if validation fails
     */
    public function __construct($message = "")
    {
        $this->message = $message;
    }

    /**
     * @param BaseForm $form
     * @param Field $field
     * @throws StopValidation
     */
    function __invoke(BaseForm $form, Field $field)
    {
        if (is_null($field->raw_data) || !$field->raw_data[0]) {
            if ($this->message == "") {
                $message = $field->gettext("This field is required.");
            } else {
                $message = $this->message;
            }
            $field->errors = [];
            throw new StopValidation($message);
        }
    }
}