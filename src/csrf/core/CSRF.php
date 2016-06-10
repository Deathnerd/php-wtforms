<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/20/2016
 * Time: 10:32 PM
 */

namespace WTForms\CSRF\Core;


use WTForms\Exceptions\NotImplemented;
use WTForms\Exceptions\ValidationError;
use WTForms\Form;

class CSRF
{
    /**
     * @var string
     */
    public $field_class = '\WTForms\CSRF\Core\CSRFTokenField';

    public function setupForm(Form $form)
    {
        $meta = $form->meta;
        $field_name = $meta->csrf_field_name;
        $unbound_field = (new \ReflectionClass($this->field_class))->newInstance([
            'label'     => 'CSRF Token',
            'csrf_impl' => $this
        ]);

        return [$field_name, $unbound_field];
    }

    /**
     * Implementations must override this to provide a method with which one
     * can get a CSRF token for this form.
     *
     * A CSRF token is usually a string that is generated deterministically
     * based on some sort of user data, though it can be anything which you
     * can validate on a subsequent request.
     *
     * @param CSRFTokenField $csrf_token_field The field which is being used for CSRF
     *
     * @return string
     * @throws \WTForms\Exceptions\NotImplemented
     */
    public function generateCSRFToken(
        /** @noinspection PhpUnusedParameterInspection */
        CSRFTokenField $csrf_token_field
    ) {
        throw new NotImplemented();
    }

    /**
     * Override this method to provide custom CSRF validation logic.
     *
     * The default CSRF validation logic simply checks if the recently
     * generated token equals the one we received as formdata.
     *
     * @param Form           $form  The form which has this CSRF token
     * @param CSRFTokenField $field The CSRF token field
     *
     * @throws ValidationError
     */
    public function validate_csrf_token(
        /** @noinspection PhpUnusedParameterInspection */
        Form $form,
        CSRFTokenField $field
    ) {
        if ($field->current_token !== $field->data) {
            throw new ValidationError("Invalid CSRF Token");
        }
    }
}