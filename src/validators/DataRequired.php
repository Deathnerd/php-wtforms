<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/27/2016
 * Time: 7:52 PM
 */

namespace WTForms\Validators;

use WTForms\Exceptions\StopValidation;
use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Checks the field's data is 'truthy' otherwise stops the validation chain.
 *
 * This validator checks that the ``data`` attribute on the field is a 'true'
 * value (effectively, it does ``if field.data``.) Furthermore, if the data
 * is a string type, a string containing only whitespace characters is
 * considered false.
 *
 * If the data is empty, also removes prior errors (such as processing errors)
 * from the field.
 *
 * **NOTE** Original Python source has a fallback for deprecated ``Required`` class.
 * This port does not have it. You're more than welcome to extend it yourself.
 * @package WTForms\Validators
 */
class DataRequired extends Validator
{

    /**
     * @var array
     */
    public $field_flags = ['required'];

    /**
     * DataRequired constructor.
     *
     * @param string $message Error message to raise in case of a validation error
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
    public function __invoke(Form $form, Field $field, $message = "")
    {
        if (is_null($field->data) || (is_string($field->data) && trim($field->data) == "")) {
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
