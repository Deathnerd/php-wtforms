<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 10:09 PM
 */

namespace WTForms\Validators;

use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Core\Field;
use WTForms\Form;


/**
 * Validates the field against a user-provided regular expression
 * @package WTForms\Validators
 */
class Regexp extends Validator
{
    /**
     * @var string
     */
    public $regex;

    /**
     * Assertion check is done here for a valid regular expression
     *
     * @param string $message Error message to raise in case of a validation error
     * @param array  $options
     */
    public function __construct($message = "", array $options = ['regex' => ''])
    {
        assert(preg_match($options['regex'], null) !== false, "Invalid regular expression passed to Regexp");
        $this->regex = $options['regex'];
        $this->message = $message;
    }

    /**
     * @param Form   $form
     * @param Field  $field
     * @param string $message
     *
     * @return mixed The first match of the regular expression if one was found
     * @throws ValidationError
     */
    public function __invoke(Form $form, Field $field, $message = "")
    {
        $match = preg_match($this->regex, $field->data ?: '', $matches);
        if (!$match) {
            if ($message == "") {
                if ($this->message == "") {
                    $message = "Invalid Input.";
                } else {
                    $message = $this->message;
                }
            }
            throw new ValidationError($message);
        }

        return $matches[0];
    }
}
