<?php

declare(strict_types=1);

namespace WTForms\CSRF\Options;

use ReflectionClass;
use WTForms\CSRF\Core\CSRF;
use WTForms\Fields\Options\FieldOptions;

/**
 * Options for the CSRFTokenField.
 *
 * @phpstan-type CSRFTokenFieldOptionsArray array{
 *     csrf_impl?: class-string<CSRF>|CSRF
 * }
 * @extends FieldOptions<CSRFTokenFieldOptionsArray>
 */
class CSRFTokenFieldOptions extends FieldOptions
{
  /** @var class-string<CSRF>|CSRF */
  public string|CSRF $csrf_impl = 'WTForms\CSRF\Core\CSRF';

  /**
   * @phpstan-param \WTForms\CSRF\Core\CSRFTokenField $obj
   * @return \WTForms\CSRF\Core\CSRFTokenField
   */
  public function hydrateObject(object $obj): object
  {
    if ($this->csrf_impl instanceof CSRF) {
      $obj->csrf_impl = $this->csrf_impl;
    } else {
      $obj->csrf_impl = (new ReflectionClass($this->csrf_impl))->newInstance();
    }
    return $obj;
  }

  protected function getDefaults(): array
  {
    return array_merge(parent::getDefaults(), [
      'label' => 'CSRF Token',
      'csrf_impl' => 'WTForms\CSRF\Core\CSRF',
    ]);
  }
}
