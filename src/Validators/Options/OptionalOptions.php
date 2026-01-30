<?php

declare(strict_types=1);

namespace WTForms\Validators\Options;

use WTForms\Internal\Types\ArrayOptions;

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

  public function hydrateObject(object $obj): object
  {
    $obj->strip_whitespace = $this->strip_whitespace;
    return $obj;
  }

  protected function getDefaults(): array
  {
    return ['strip_whitespace' => true];
  }
}
