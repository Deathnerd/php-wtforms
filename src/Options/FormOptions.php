<?php

declare(strict_types=1);

namespace WTForms\Options;

use WTForms\DefaultMeta;
use WTForms\Form;
use WTForms\Internal\Types\ArrayOptions;
use WTForms\Internal\Types\UnsetType;

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
 *
 * @extends ArrayOptions<FormOptionsArray>
 */
class FormOptions extends ArrayOptions
{
  /** @var array<string, mixed>|DefaultMeta|UnsetType */
  public array|DefaultMeta|UnsetType $meta;

  public string $prefix = '';

  /** @var array<string, mixed> */
  public array $formdata = [];

  public object $obj;

  /** @var array<string, mixed>|UnsetType */
  public array $data;

  /**
   * @phpstan-param Form $obj
   *
   * @return Form
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
      'meta'     => new UnsetType(),
      'prefix'   => '',
      'formdata' => [],
      'obj'      => new UnsetType(),
      'data'     => new UnsetType(),
    ];
  }
}
