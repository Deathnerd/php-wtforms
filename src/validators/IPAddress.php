<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 10:24 PM
 */

namespace WTForms\Validators;


use WTForms\Exceptions\ValidationError;
use WTForms\Exceptions\ValueError;
use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Validates an IP Address
 * @package WTForms\Validators
 */
class IPAddress extends Validator
{
    /**
     * @var bool
     */
    public $ipv4;

    /**
     * @var int
     */
    public $ip_type;

    /**
     * IPAddress constructor.
     *
     * @param string $message Error message to raise in case of a validation error.
     * @param array  $options
     *
     * @throws ValueError If ip_type isn't a proper flag
     *
     */
    public function __construct($message = "", array $options = ['ip_type' => FILTER_FLAG_IPV4])
    {
        if (!in_array($options['ip_type'], [FILTER_FLAG_IPV4, FILTER_FLAG_IPV6])) {
            throw new ValueError("IP Address Validator must have FILTER_FLAG_IPV4 OR FILTER_FLAG_IPV6 passed as first argument");
        }
        $this->ip_type = $options['ip_type'];
        $this->message = $message;
    }

    /**
     * @param Form   $form
     * @param Field  $field
     * @param string $message
     *
     * @throws ValidationError
     */
    public function __invoke(Form $form, Field $field, $message = "")
    {
        $value = $field->data;
        $valid = false;
        if (!is_null($value)) {
            $valid = filter_var($value, FILTER_VALIDATE_IP, $this->ip_type);
        }
        if (!$valid) {
            $message = $this->message;
            if ($message == "") {
                $message = "Invalid IP address.";
            }
            throw new ValidationError($message);
        }
    }
}