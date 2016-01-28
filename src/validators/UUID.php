<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 11:21 PM
 */

namespace Deathnerd\WTForms\Validators;

use Deathnerd\WTForms\BaseForm;
use Deathnerd\WTForms\Fields\Field;

/**
 * Validates a UUID
 * @package Deathnerd\WTForms\Validators
 */
class UUID extends Regexp
{
    /**
     * UUID constructor.
     * @param string $message Error message to raise in case of validation error
     */
    public function __construct($message = "")
    {
        parent::__construct('^[0-9a-fA-F]{8}-([0-9a-fA-F]{4}-){3}[0-9a-fA-F]{12}$', $message);
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
        $message = $this->message;
        if ($message == "") {
            $message = $field->gettext("Invalid URL.");
        }
        return parent::__invoke($form, $field, $message);
    }

}