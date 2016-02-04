<?php
namespace Deathnerd\WTForms\CSRF\Core;

use Deathnerd\WTForms;
use Deathnerd\WTForms\Fields\Core\HiddenField;
use Deathnerd\WTForms\Form;
use Deathnerd\WTForms\Utils\UnsetValue;
use Deathnerd\WTForms\Validators\ValidationError;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
    public function __construct(array $kwargs = [])
    {
        $kwargs = (new OptionsResolver())->setDefaults([
            'label' => 'CSRF Token',
            'csrf_impl' => 'CSRF'
        ])->resolve($kwargs);

        // TODO: Might not work. Revisit
        $this->csrf_impl = new $kwargs['csrf_impl']();
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

    /**
     * @param $formdata
     * @param null $data
     * @throws WTForms\NotImplemented
     */
    public function process($formdata, $data = null)
    {
        if ($data === null) {
            $data = new UnsetValue();
        }
        parent::process($formdata, $data);
        $this->current_token = $this->csrf_impl->generate_csrf_token($this);
    }
}