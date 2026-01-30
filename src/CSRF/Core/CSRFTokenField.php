<?php

namespace WTForms\CSRF\Core;

use ReflectionClass;
use WTForms;
use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Simple\HiddenField;
use WTForms\Form;

/**
 * @template TCSRFImpl of CSRF
 *
 * @phpstan-type CSRFTokenFieldOptions array{label?:string, csrf_impl?: class-string<TCSRFImpl>|TCSRFImpl, ...}
 */
class CSRFTokenField extends HiddenField
{
  /**
   * The current CSRF token.
   */
  public string $current_token = '';

  /**
   * @var TCSRFImpl
   */
  public $csrf_impl;

  /**
   * CSRFTokenField constructor.
   *
   * @param CSRFTokenFieldOptions $options
   */
  public function __construct(array $options = [])
  {
    /** @var CSRFTokenFieldOptions $defaults */
    $defaults = ['label' => 'CSRF Token', 'csrf_impl' => 'WTForms\CSRF\Core\CSRF'];
    $options = array_merge($defaults, $options);

    // Accept either a class name string or an already-instantiated CSRF object
    if ($options['csrf_impl'] instanceof CSRF) {
      $this->csrf_impl = $options['csrf_impl'];
    } else {
      $this->csrf_impl = (new ReflectionClass($options['csrf_impl']))->newInstance();
    }
    unset($options['csrf_impl']);
    parent::__construct($options);
  }

  /**
   * We want to always return the current token on render, regardless of
   * whether a good or bad token was passed.
   *
   * @internal
   *
   * @return string
   */
  public function __get($name)
  {
    return 'value' == $name ? $this->current_token : null;
  }

  /**
   * Don't populate objects with CSRF Token.
   */
  public function populateObj(object $obj, string $name): void
  {
    // Intentionally do nothing - CSRF tokens shouldn't be stored in objects
  }

  /**
   * Handle the validation of this token field.
   *
   * @throws ValidationError
   */
  public function preValidate(Form $form): void
  {
    $this->csrf_impl->validate_csrf_token($form, $this);
  }

  /**
   * @param array<string, mixed> $formdata
   * @param null                 $data
   *
   * @throws WTForms\Exceptions\NotImplemented
   */
  public function process(array $formdata, $data = null): void
  {
    parent::process($formdata, $data);
    $this->current_token = $this->csrf_impl->generateCSRFToken($this);
  }
}
