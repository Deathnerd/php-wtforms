<?php

declare(strict_types=1);

namespace WTForms\Options;

use WTForms\DefaultMeta;
use WTForms\Internal\Types\ArrayOptions;

/**
 * Options for Form constructors.
 *
 * @phpstan-type FormOptionsArray array{
 *     meta?: array<string, mixed>|DefaultMeta|null,
 *     prefix?: string,
 *     formdata?: array<string, mixed>,
 *     obj?: object|null,
 *     data?: array<string, mixed>|null
 * }
 * @extends ArrayOptions<FormOptionsArray>
 */
class FormOptions extends ArrayOptions
{
  /** @var array<string, mixed>|DefaultMeta|null */
  public array|DefaultMeta|null $meta = null;

  public string $prefix = '';

  /** @var array<string, mixed> */
  public array $formdata = [];

  public ?object $obj = null;

  /** @var array<string, mixed>|null */
  public ?array $data = null;

  /**
   * @phpstan-param \WTForms\Form $obj
   * @return \WTForms\Form
   */
  public function hydrateObject(object $obj): object
  {
    if ($this->prefix) {
      $obj->prefix = $this->prefix;
      // Normalize prefix
      if (!str_contains('-_;:/.', substr($obj->prefix, -1))) {
        $obj->prefix .= '-';
      }
    }
    return $obj;
  }

  protected function getDefaults(): array
  {
    return [
      'meta' => null,
      'prefix' => '',
      'formdata' => [],
      'obj' => null,
      'data' => null,
    ];
  }
}
