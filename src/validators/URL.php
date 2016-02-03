<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 10:58 PM
 */

namespace Deathnerd\WTForms\Validators;

use Deathnerd\WTForms\BaseForm;
use Deathnerd\WTForms\Fields\Core\Field;

/**
 * Simple url validation using PHP's filter_var.
 * @package Deathnerd\WTForms\Validators
 */
class URL extends Validator
{
    //TODO Investigate whether relying on PHP's filter_var is okay for this

    /**
     * URL constructor.
     * @param string $message Error message to raise in case of a validation error
     */
    public function __construct($message = "")
    {
        $this->message = $message;
    }

    /**
     * @param BaseForm $form
     * @param Field $field
     * @throws ValidationError
     */
    public function __invoke(BaseForm $form, Field $field)
    {
        if (is_null($field->data)) {
            if (preg_match("^(?>(?>([a-f0-9]{1,4})(?>:(?1)){7}|(?!(?:.*[a-f0-9](?>:|$)){8,})((?1)(?>:(?1)){0,6})?::(?2)?)|(?>(?>(?1)(?>:(?1)){5}:|(?!(?:.*[a-f0-9]:){6,})(?3)?::(?>((?1)(?>:(?1)){0,4}):)?)?(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])(?>\.(?4)){3}))$", $field->data, $match)) {
                $valid = filter_var($match[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($match[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
            } else {
                $valid = filter_var($field->data, FILTER_VALIDATE_URL);
            }
            if (!$valid) {
                $message = $this->message;
                if ($message == "") {
                    $message = $field->gettext("Invalid URL.");
                }
                throw new ValidationError($message);
            }
        }
    }
}