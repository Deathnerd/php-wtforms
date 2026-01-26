<?php

declare(strict_types=1);

namespace WTForms\Validators\Options;

/**
 * Options for the Optional validator.
 *
 * @phpstan-type OptionalOptionsArray array{strip_whitespace?:bool}
 *
 * @extends ArrayOptions<OptionalOptionsArray>
 */
class OptionalOptions extends ArrayOptions
{
  public bool $strip_whitespace = true;

  protected function getDefaults(): array
  {
    return ['strip_whitespace' => true];
  }
}
