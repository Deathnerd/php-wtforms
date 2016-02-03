<?php
namespace Deathnerd\WTForms\CSRF;

use Deathnerd\WTForms;
use Deathnerd\WTForms\Fields\Core\HiddenField;
use Deathnerd\WTForms\Form;
use Deathnerd\WTForms\Validators\ValidationError;

class CSRFTokenField extends HiddenField
{
    /**
     * The current CSRF token
     * @var string
     */
    public $current_token = "";

    /**
     * @var CSRF
     */
    public $csrf_impl;

    /**
     * CSRFTokenField constructor.
     * @param array $kwargs
     */
    public function __construct(array $kwargs = ['label' => 'CSRF Token', 'csrf_impl' => 'CSRF'])
    {
        // TODO: Might not work. Revisit
        $this->csrf_impl = new ${$kwargs['csrf_impl']}();
        unset($kwargs['csrf_impl']);
        $label = $kwargs['label'];
        unset($kwargs['label']);
        parent::__construct($label, $kwargs);
    }


    /**
     * We want to always return the current token on render, regardless of
     * whether a good or bad token was passed
     * @return string
     */
    public function _value()
    {
        return $this->current_token;
    }

    /**
     * Don't populate objects with CSRF Token
     */
    public function populate_obj()
    {
    }

    /**
     * Handle the validation of this token field.
     * @param Form $form
     * @throws ValidationError
     */
    public function pre_validate(Form $form)
    {
        $this->csrf_impl->validate_csrf_token($form, $this);
    }

    public function process($formdata, $data = UNSET_VALUE)
    {
        parent::process($formdata, $data);
        $this->current_token = $this->csrf_impl->generate_csrf_token($this);
    }
}