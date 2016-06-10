<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/27/2016
 * Time: 7:33 PM
 */

namespace WTForms\Validators;

use WTForms\Exceptions\StopValidation;
use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Allows empty input and stops the validation chain from continuing.
 *
 * If input is empty, also removes prior errors (such as processing errors)
 * from the field
 * @package WTForms\Validators
 */
class Optional extends Validator
{
    /**
     * @var array
     */
    public $field_flags = ['optional'];

    /**
     * @var callable
     */
    public $string_check;

    /**
     * @var bool
     */
    private $strip_whitespace;

    /**
     * Optional constructor.
     *
     * @param string $message
     * @param array  $options
     *
     */
    public function __construct($message = "", array $options = ['strip_whitespace' => true])
    {
        $this->strip_whitespace = $options['strip_whitespace'];
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
        if (is_null($field->raw_data)
            || (is_string($field->raw_data[0]) && $this->stringCheck($field->raw_data[0]) == "")
        ) {
            $field->errors = [];
            throw new StopValidation();
        }
    }

    private function stringCheck($string)
    {
        return ($this->strip_whitespace) ? trim($string) : $string;
    }
}