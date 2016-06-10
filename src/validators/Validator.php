<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/21/2016
 * Time: 11:15 AM
 */

namespace WTForms\Validators;

use WTForms\Exceptions\NotImplemented;
use WTForms\Fields\Core\Field;
use WTForms\Form;

class Validator
{
    /**
     * @var string Error message to raise in case of a validation error
     */
    public $message;
    /**
     * @var array
     */
    public $field_flags = [];

    public function __construct($message = "", array $options = [])
    {
        throw new NotImplemented("Validator must have an overridden __construct method");
    }

    /**
     * @param Form   $form
     * @param Field  $field
     * @param string $message
     *
     * @throws NotImplemented
     */
    public function __invoke(Form $form, Field $field, $message = "")
    {
        throw new NotImplemented("Validator must have an overridden __invoke method");
    }
}

