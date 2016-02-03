<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 10:45 PM
 */

namespace Deathnerd\WTForms\Validators;


use Deathnerd\WTForms\BaseForm;
use Deathnerd\WTForms\Fields\Core\Field;

/**
 * Validates a MAC Address
 * @package Deathnerd\WTForms\Validators
 */
class MacAddress extends Validator
{
    /**
     * MacAddress constructor.
     * @param string $message Error message to raise in case of a validation error.
     */
    public function __construct($message = "")
    {
        $this->message = $message;
    }

    /**
     * @param BaseForm $form
     * @param Field $field
     * @param string $message
     * @return mixed
     * @throws ValidationError
     */
    function __invoke(BaseForm $form, Field $field, $message = "")
    {
        $value = $field->data;
        $valid = false;
        if (!is_null($value)) {
            $valid = filter_var($value, FILTER_VALIDATE_MAC);
        }
        if (!$valid) {
            $message = $this->message;
            if ($message == "") {
                $message = $field->gettext("Invalid MAC address.");
            }
            throw new ValidationError($message);
        }
    }
}