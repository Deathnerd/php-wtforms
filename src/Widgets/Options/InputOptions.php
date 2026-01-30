<?php

declare(strict_types=1);

namespace WTForms\Widgets\Options;

use WTForms\Internal\Types\ArrayOptions;

/**
 * Options for the Input widget.
 *
 * @phpstan-type InputOptionsArray array{type?: string}
 * @extends ArrayOptions<InputOptionsArray>
 */
class InputOptions extends ArrayOptions
{
  public string $type = '';

  public function hydrateObject(object $obj): object
  {
    $obj->type = $this->type;
    return $obj;
  }

  protected function getDefaults(): array
  {
    return ['type' => ''];
  }
}
