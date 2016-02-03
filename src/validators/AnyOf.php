<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 11:26 PM
 */

namespace Deathnerd\WTForms\Validators;

use Deathnerd\WTForms\BaseForm;
use Deathnerd\WTForms\Fields\Core\Field;
use stdClass;


/**
 * Compares the incoming data to a sequence of valid inputs
 * @package Deathnerd\WTForms\Validators
 */
class AnyOf extends Validator
{
    /**
     * @var array
     */
    public $values;
    /**
     * @var callable|null
     */
    public $values_formatter;


    /**
     * AnyOf constructor.
     * @param array $values A sequence of valid inputs
     * @param string $message Error message to raise in case of a validation error. TODO: User interpolation
     * @param callable|null $values_formatter Function used to format the list of values in the error message
     */
    public function __construct(array $values, $message = "", $values_formatter = null)
    {
        $this->values = $values;
        $this->message = $message;
        $this->values_formatter = new stdClass();
        if (!is_null($values_formatter)) {
            $this->values_formatter->callback = $values_formatter;
        }
    }

    /**
     * If a callable was passed as ``$values_formatter`` in the constructor, this
     * will call that to format the values to a string. Otherwise calls a default
     * formatter: ``implode``
     * @param array $values The values to format
     * @return string The formatted string
     */
    protected function formatter(array $values)
    {
        if (is_callable($this->values_formatter, 'callback')) {
            $this->values_formatter->callback->__invoke($values);
        }
        return implode(", ", $values);
    }

    /**
     * @param BaseForm $form
     * @param Field $field
     * @throws ValidationError
     */
    public function __invoke(BaseForm $form, Field $field)
    {
        if (!in_array($this->values, $field->data)) {
            $message = $this->message;
            $value_string = $this->formatter($this->values);
            if ($message == "") {
                $message = sprintf($field->gettext("Invalid value, must be one of: %s."), $value_string);
            }
            throw new ValidationError($message);
        }
    }
}