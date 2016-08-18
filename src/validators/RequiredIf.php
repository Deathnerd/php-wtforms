<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/30/2015
 * Time: 3:46 PM
 */
namespace WTForms\Validators;

use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Makes a field required if another field has a value
 */
class RequiredIf extends DataRequired
{
    /**
     * @var string
     */
    public $fieldname;

    /**
     * @param string $message Error message to raise in case of a validation error.
     * @param array  $options
     *
     * @throws \TypeError
     */
    public function __construct($message = "", array $options = ['fieldname' => ''])
    {
        if (!array_key_exists('fieldname', $options) || !$options['fieldname']) {
            throw new \RuntimeException("RequiredIf requires fieldname!");
        }

        $this->fieldname = $options['fieldname'];
        parent::__construct($message, $options);
    }

    /**
     * @param Form   $form
     * @param Field  $field
     * @param string $message
     *
     * @throws ValidationError
     */
    public function __invoke(Form $form, Field $field = null, $message = "")
    {
        if ($form->{$this->fieldname} !== null) {
            /** @var Field $other */
            $other = $form->{$this->fieldname};
        } else {
            throw new ValidationError("Invalid field name '{$this->fieldname}'");
        }

        if ($other->data) {
            parent::__invoke($form, $field, $message);
        }
    }
}
