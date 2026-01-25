<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/27/2016
 * Time: 8:04 PM
 */

namespace WTForms\Validators;

use WTForms\Exceptions\StopValidation;
use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Validates that input was provided for this field.
 *
 * Note there is a distinction between this and {@link DataRequired} in that
 * {@link InputRequired} looks that form-input data was provided, and {@link DataRequired}
 * looks at the post-coercion data
 *
 * @template-extends Validator<array{}>
 */
class InputRequired extends Validator
{
    /**
     * @var array
     */
    public array $field_flags = ['required'];

    /**
     * InputRequired constructor.
     *
     * @param string $message Message to raise if validation fails
     * @param array  $options
     */
    public function __construct(string $message = "", array $options = [])
    {
        parent::__construct($message, $options);
    }

    /**
     * @param Form   $form
     * @param Field  $field
     * @param string $message
     *
     * @return void
     *
     * @throws StopValidation
     * @phpstan-assert !null $field->raw_data
     */
    public function __invoke(Form $form, Field $field, string $message = "")
    {
        if (empty($field->raw_data) || !$field->raw_data[0]) {
            if ($this->message == "") {
                $message = "This field is required.";
            } else {
                $message = $this->message;
            }
            $field->errors = [];
            throw new StopValidation($message);
        }
    }
}
