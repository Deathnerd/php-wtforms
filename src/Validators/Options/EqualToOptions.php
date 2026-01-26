<?php

declare(strict_types=1);

namespace WTForms\Validators\Options;

use WTForms\Internal\Types\ArrayOptions;

/**
 * Options for the EqualTo validator.
 *
 * @phpstan-type EqualToOptionsArray array{fieldname?:string}
 *
 * @extends ArrayOptions<EqualToOptionsArray>
 */
class EqualToOptions extends ArrayOptions
{
  public string $fieldname = '';

  protected function getDefaults(): array
  {
    return ['fieldname' => ''];
  }
}
