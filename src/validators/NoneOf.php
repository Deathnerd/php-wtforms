<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 11:56 PM
 */

namespace WTForms\Validators;


use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Compares the incoming data to a sequence of invalid objects
 * @package WTForms\Validators
 */
class NoneOf extends AnyOf
{
    /**
     * NoneOf Constructor
     * @inheritdoc
     */
    public function __construct($message = "", array $options = ['values' => []])
    {
        parent::__construct($message, $options);
    }

    /**
     * @inheritdoc
     */
    public function __invoke(Form $form, Field $field, $message = "")
    {
        if (in_array($field->data, $this->values)) {
            $message = $this->message;
            $values_string = $this->formatter($this->values);
            if ($message == "") {
                $message = "Invalid value, can't be any of: $values_string.";
            } else {
                $message = sprintf($message, $values_string);
            }
            throw new ValidationError($message);
        }
    }
}
