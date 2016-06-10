<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 11:21 PM
 */

namespace WTForms\Validators;

use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Validates a UUID
 * @package WTForms\Validators
 */
class UUID extends Regexp
{
    /**
     * UUID constructor.
     *
     * @param string $message Error message to raise in case of validation error
     */
    public function __construct($message = "")
    {
        parent::__construct($message, ['regex' => '/^[0-9a-fA-F]{8}-([0-9a-fA-F]{4}-){3}[0-9a-fA-F]{12}$/']);
    }

    /**
     * @param Form   $form
     * @param Field  $field
     * @param string $message
     *
     * @return mixed
     * @throws ValidationError
     */
    public function __invoke(Form $form, Field $field, $message = "")
    {
        $message = $this->message;
        if (!$message) {
            $message = "Invalid UUID.";
        }

        parent::__invoke($form, $field, $message);
    }
}
