<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/31/2015
 * Time: 11:34 AM.
 */

namespace WTForms;

use WTForms\CSRF\Session\SessionCSRF;
use WTForms\Exceptions\TypeError;
use WTForms\Fields\Core\Field;

/**
 * This is the default Meta class which defines all the default values
 * and therefore is also the 'API' of the class Meta interface.
 */
class DefaultMeta
{
  public string $csrf_field_name = 'csrf_token';
  public ?string $csrf_time_limit = null;
  public bool $csrf = false;
  public ?string $csrf_secret = null;
  /**
   * @var ?class-string<covariant \WTForms\CSRF\Core\CSRF>
   */
  public ?string $csrf_class = null;

  public function renderField(Field $field, array $render_kw = [])
  {
    $other_kw = $field->render_kw;
    if (!empty($other_kw)) {
      $render_kw = array_merge($other_kw, $render_kw);
    }
    $widget = $field->widget;

    return $widget($field, $render_kw);
  }

  /**
   * Build a CSRF implementation. This is called once per form instance
   * The default implementation builds the class referenced to by
   * {@link csrf_class} with zero arguments. If {@link csrf_class} is ``null``,
   * will instead use the default implementation {@link \WTForms\CSRF\Session\SessionCSRF}.
   *
   * @param Form $form The form
   *
   * @return object|SessionCSRF A CSRF representation
   *
   * @noinspection PhpUnusedParameterInspection
   */
  public function buildCSRF(Form $form)
  {
    if (!is_null($this->csrf_class)) {
      try {
        return (new \ReflectionClass($this->csrf_class))->newInstance();
      } catch (\ReflectionException $e) {
        throw new TypeError(sprintf('csrf_class must be a valid class name (got %s)', $this->csrf_class));
      }
    }

    return new SessionCSRF();
  }

  /**
   * Given an associative array of values, update values on this `Meta` instance.
   */
  public function updateValues(array $values = [])
  {
    foreach ($values as $key => $value) {
      $this->$key = $value;
    }
  }
}
