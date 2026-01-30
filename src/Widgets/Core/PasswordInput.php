<?php

declare(strict_types=1);

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;
use WTForms\Widgets\Options\PasswordInputOptions;

/**
 * Render a password input.
 *
 * For security purposes, this field will not reproduce the value on a form
 * submit by default. To have the value filled in, set the `$hide_value` to
 * `false`.
 *
 * @extends Input<string>
 */
class PasswordInput extends Input
{
  public bool $hide_value;

  public function __construct(bool|PasswordInputOptions $hide_value = true)
  {
    if ($hide_value instanceof PasswordInputOptions) {
      parent::__construct('password');
      $this->constructFromOptions($hide_value);
      return;
    }
    parent::__construct('password');
    $this->hide_value = $hide_value;
  }

  public function __invoke(Field $field, array $options = []): string
  {
    if ($this->hide_value) {
      $options['value'] = '';
    }

    return parent::__invoke($field, $options);
  }
}
