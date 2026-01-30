<?php

declare(strict_types=1);

namespace WTForms\Widgets\Options;

use WTForms\Internal\Types\ArrayOptions;

/**
 * Options for the PasswordInput widget.
 *
 * @phpstan-type PasswordInputOptionsArray array{hide_value?: bool}
 * @extends ArrayOptions<PasswordInputOptionsArray>
 */
class PasswordInputOptions extends ArrayOptions
{
  public bool $hide_value = true;

  public function hydrateObject(object $obj): object
  {
    $obj->hide_value = $this->hide_value;
    return $obj;
  }

  protected function getDefaults(): array
  {
    return ['hide_value' => true];
  }
}
