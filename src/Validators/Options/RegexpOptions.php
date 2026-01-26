<?php

declare(strict_types=1);

namespace WTForms\Validators\Options;

use WTForms\Internal\Types\ArrayOptions;

/**
 * Options for the Regexp validator.
 *
 * @phpstan-type RegexpOptionsArray array{regex?:string}
 *
 * @extends ArrayOptions<RegexpOptionsArray>
 */
class RegexpOptions extends ArrayOptions
{
  public string $regex = '';

  protected function getDefaults(): array
  {
    return ['regex' => ''];
  }
}
