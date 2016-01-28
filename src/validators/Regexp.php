<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 10:09 PM
 */

namespace Deathnerd\WTForms\Validators;

use Deathnerd\WTForms\BaseForm;
use Deathnerd\WTForms\Fields\Field;


/**
 * Validates the field against a user-provided regular expression
 * @package Deathnerd\WTForms\Validators
 */
class Regexp extends Validator
{
    /**
     * @var string
     */
    public $regex;
    /**
     * @var string
     */
    public $message;

    /**
     * Assertion check is done here for a valid regular expression
     * @param string $regex The regular expression string to use.
     * @param string $message Error message to raise in case of a validation error
     */
    public function __construct($regex, $message = "")
    {
        assert(filter_var($regex, FILTER_VALIDATE_REGEXP), "Invalid regular expression passed to Regexp");
        $this->regex = $regex;
        $this->message = $message;
    }

    /**
     * @param BaseForm $form
     * @param Field $field
     * @param string $message
     * @return mixed The first match of the regular expression if one was found
     * @throws ValidationError
     */
    function __invoke(BaseForm $form, Field $field, $message = "")
    {
        $match = preg_match($this->regex, !is_null($field->data) ? $field->data : '', $matches);
        if (!$match) {
            if ($message == "") {
                if ($this->message == "") {
                    $message = $field->gettext("Invalid Input.");
                } else {
                    $message = $this->message;
                }
            }
            throw new ValidationError($message);
        }
        return $matches[0];
    }


}