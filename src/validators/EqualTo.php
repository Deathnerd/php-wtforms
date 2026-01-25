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
use WTForms\Validators\Options\EqualToOptions;

/**
 * Compares the values of two fields
 * @phpstan-import-type EqualToOptionsArray from EqualToOptions
 * @template-extends Validator<EqualToOptionsArray>
 */
class EqualTo extends Validator
{
    /**
     * @var string
     */
    public $fieldname;

    /**
     * @param string $message Error message to raise in case of a validation error.
     * @param EqualToOptionsArray|EqualToOptions $options
     *
     * @throws \RuntimeException
     */
    public function __construct(string $message = "", $options = [])
    {
        if ($options instanceof ArrayOptions) {
            $options = $options->toArray();
        }
        parent::__construct($message, $options);
        if (!array_key_exists('fieldname', $options) || !$options['fieldname']) {
            throw new \RuntimeException("EqualTo requires fieldname!");
        }
        $this->fieldname = $options['fieldname'];
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
            throw new ValidationError(sprintf("Invalid field name '%s'", $this->fieldname));
        }

        if ($field->data != $other->data) {
            $message = $this->message;
            $d = [
                "other_name"  => $this->fieldname,
                "other_label" => property_exists($other->label, 'text') ? $other->label->text : $this->fieldname
            ];
            if ($message == "") {
                $message = "Field must be equal to %(other_name)s";
            }
            throw new ValidationError(vsprintf_named($message, $d));
        }
    }
}
