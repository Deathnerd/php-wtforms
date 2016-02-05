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
use Deathnerd\WTForms\Interfaces\FormatterInterface;


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
     * @var FormatterInterface
     */
    public $values_formatter;


    /**
     * AnyOf constructor.
     * @param array $values A sequence of valid inputs
     * @param string $message Error message to raise in case of a validation error. TODO: User interpolation
     * @param FormatterInterface|null $values_formatter Function used to format the list of values in the error message
     */
    public function __construct(array $values, $message = "", FormatterInterface $values_formatter = null)
    {
        $this->values = $values;
        $this->message = $message;
        $this->values_formatter = $values_formatter;
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
        if ($this->values_formatter instanceof FormatterInterface) {
            return $this->values_formatter->run(...$values);
        }
        return implode(", ", $values);
    }

    /**
     * @param BaseForm $form
     * @param Field $field
     * @param string $message
     * @throws ValidationError
     */
    public function __invoke(BaseForm $form, Field $field, $message="")
    {
        if (!in_array($field->data, $this->values)) {
            $message = $this->message;
            $value_string = $this->formatter($this->values);
            if ($message == "") {
                $message = sprintf($field->gettext("Invalid value, must be one of: %s."), $value_string);
            } else {
                $message = sprintf($field->gettext($message), $value_string);
            }
            throw new ValidationError($message);
        }
    }
}