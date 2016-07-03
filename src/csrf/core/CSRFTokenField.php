<?php
namespace WTForms\CSRF\Core;

use WTForms;
use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Simple\HiddenField;
use WTForms\Form;

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
     *
     * @param array $options
     *   $form
     */
    public function __construct(array $options = [])
    {
        $defaults = ['label' => 'CSRF Token', 'csrf_impl' => 'WTForms\CSRF\Core\CSRF'];
        $options = array_merge($defaults, $options);

        $this->csrf_impl = (new \ReflectionClass($options['csrf_impl']))->newInstance();
        unset($options['csrf_impl']);
        parent::__construct($options);
    }

    /**
     * We want to always return the current token on render, regardless of
     * whether a good or bad token was passed
     * @internal
     * @return string
     */
    public function __get($name)
    {
        return $name == "value" ? $this->current_token : null;
    }

    /**
     * Don't populate objects with CSRF Token
     */
    public function populateObj($obj, $name)
    {
        return $obj;
    }

    /**
     * Handle the validation of this token field.
     *
     * @param Form $form
     *
     * @throws ValidationError
     */
    public function preValidate(Form $form)
    {
        $this->csrf_impl->validate_csrf_token($form, $this);
    }

    /**
     * @param      $formdata
     * @param null $data
     *
     * @throws WTForms\Exceptions\NotImplemented
     */
    public function process($formdata, $data = null)
    {
        parent::process($formdata, $data);
        $this->current_token = $this->csrf_impl->generateCSRFToken($this);
    }
}