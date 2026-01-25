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
use WTForms\Validators\Options\ArrayOptions;
use WTForms\Validators\Options\RequiredIfOptions;

/**
 * Makes a field required if another field has a value
 * @phpstan-import-type RequiredIfOptionsArray from RequiredIfOptions
 */
class RequiredIf extends DataRequired
{
    /**
     * @var string
     */
    public $fieldname;

    /**
     * @param string $message Error message to raise in case of a validation error.
     * @param RequiredIfOptionsArray|RequiredIfOptions $options
     *
     * @throws \RuntimeException
     */
    public function __construct(string $message = "", $options = [])
    {
        if ($options instanceof ArrayOptions) {
            $options = $options->toArray();
        }
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
    public function __invoke(Form $form, Field $field, string $message = "")
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
