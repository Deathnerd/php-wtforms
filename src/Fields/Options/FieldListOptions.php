<?php

declare(strict_types=1);

namespace WTForms\Fields\Options;

use WTForms\Fields\Core\Field;

/**
 * Options class for FieldList constructors.
 *
 * @phpstan-type FieldListOptionsArray array{
 *     validators?: list<\WTForms\Validators\Validator|callable>,
 *     filters?: list<callable>,
 *     description?: string,
 *     id?: string|null,
 *     default?: mixed,
 *     widget?: \WTForms\Widgets\Core\Widget|null,
 *     render_kw?: array<string, mixed>,
 *     name?: string|null,
 *     prefix?: string,
 *     meta?: \WTForms\DefaultMeta|null,
 *     attributes?: array<string, mixed>,
 *     label?: string|null,
 *     class?: string|null,
 *     form?: \WTForms\Form|null,
 *     inner_field?: Field<mixed>|null,
 *     min_entries?: int,
 *     max_entries?: int|null
 * }
 */
class FieldListOptions extends FieldOptions
{
  /**
   * The field type to use for entries in the list.
   *
   * @var Field<mixed>|null
   */
  public ?Field $inner_field = null;

  /**
   * Minimum number of entries to display.
   */
  public int $min_entries = 0;

  /**
   * Maximum number of entries allowed. Null for unlimited.
   */
  public ?int $max_entries = null;

  protected function getDefaults(): array
  {
    return array_merge(parent::getDefaults(), [
      'inner_field' => null,
      'min_entries' => 0,
      'max_entries' => null,
    ]);
  }
}
