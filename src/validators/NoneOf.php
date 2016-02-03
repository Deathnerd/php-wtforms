<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 11:56 PM
 */

namespace Deathnerd\WTForms\Validators;


use Deathnerd\WTForms\BaseForm;
use Deathnerd\WTForms\Fields\Core\Field;

/**
 * Compares the incoming data to a sequence of invalid objects
 * @package Deathnerd\WTForms\Validators
 */
class NoneOf extends AnyOf
{
    /**
     * NoneOf constructor.
     * @param array $values A sequence of invalid inputs.
     * @param string $message Error message to raise in case of a validation error. //TODO: User interpolation
     * @param callable|null $values_formatter Function used to format the list of values in the error message.
     */
    public function __construct(array $values, $message = "", $values_formatter = null)
    {
        parent::__construct($values, $message, $values_formatter);
    }

    /**
     * @inheritdoc
     */
    public function __invoke(BaseForm $form, Field $field)
    {
        if (in_array($field->data, $this->values)) {
            $message = $this->message;
            $values_string = $this->formatter($this->values);
            if ($message == "") {
                $message = sprintf($field->gettext("Invalid value, can't be any of: %s."), $values_string);
            }
            throw new ValidationError($message);
        }
    }
}