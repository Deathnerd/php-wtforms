<?php

declare(strict_types=1);

namespace WTForms\Validators\Options;

use WTForms\Internal\Types\ArrayOptions;

/**
 * Options for the AnyOf validator.
 *
 * @phpstan-type AnyOfOptionsArray array{values?:array, formatter?:callable|null}
 *
 * @extends ArrayOptions<AnyOfOptionsArray>
 */
class AnyOfOptions extends ArrayOptions
{
  public array $values = [];

  /** @var callable|null */
  public $formatter;

  public function validate(): void
  {
    if (empty($this->values)) {
      throw new \WTForms\Exceptions\TypeError('Values passed to AnyOf must be in the form of an non-empty array');
    }
    if ($this->formatter !== null && !is_callable($this->formatter)) {
      throw new \WTForms\Exceptions\TypeError(sprintf('Formatter must be a callable; %s found', gettype($this->formatter)));
    }
  }

  public function hydrateObject(object $obj): object
  {
    $obj->values = $this->values;
    return $obj;
  }

  protected function getDefaults(): array
  {
    return ['values' => [], 'formatter' => null];
  }
}
