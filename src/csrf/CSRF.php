<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/20/2016
 * Time: 10:32 PM
 */

namespace Deathnerd\WTForms\CSRF;


use Deathnerd\WTForms\Fields\Field;
use Deathnerd\WTForms\Form;
use Deathnerd\WTForms\NotImplementedException;
use Deathnerd\WTForms\ValidationError;

class CSRF
{
    /**
     * @var string
     */
    public $field_class = "CSRFTokenField";

    public function setup_form(Form $form)
    {
        $meta = $form->meta;
        $field_name = $meta->csrf_field_name;
        // TODO: This might not work... Look into a better way to pass $this and dynamically call an object
        $unbound_field = new ${$this->field_class}(['label' => 'CSRF Token', 'csrf_impl' => $this]);
        return [[$field_name, $unbound_field]];
    }

    /**
     * Implementations must override this to provide a method with which one
     * can get a CSRF token for this form.
     *
     * A CSRF token is usually a string that is generated deterministically
     * based on some sort of user data, though it can be anything which you
     * can validate on a subsequent request.
     *
     * @param Field $csrf_token_field The field which is being used for CSRF
     * @throws NotImplementedException
     * @return string A generated CSRF string
     */
    public function generate_csrf_token(Field $csrf_token_field)
    {
        throw new NotImplementedException();
    }

    /**
     * Override this method to provide custom CSRF validation logic.
     *
     * The default CSRF validation logic simply checks if the recently
     * generated token equals the one we received as formdata.
     *
     * @param Form $form The form which has this CSRF token
     * @param Field $field The CSRF token field
     * @throws ValidationError
     */
    public function validate_csrf_token(Form $form, Field $field)
    {
        if ($field->current_token !== $field->data) {
            // TODO: Translations
            throw new ValidationError("Invalid CSRF Token");
        }
    }
}