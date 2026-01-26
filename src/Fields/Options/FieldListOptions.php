<?php

declare(strict_types=1);

namespace WTForms\Fields\Options;

use WTForms\Fields\Core\Field;

/**
 * Options class for FieldList constructors.
 *
 * @phpstan-import-type FieldOptionsArray from FieldOptions
 * @phpstan-type FieldListOptionsArray array{
 *     inner_field?: Field<mixed>|null,
 *     min_entries?: int,
 *     max_entries?: int|null
 * } & FieldOptionsArray
 * @extends FieldOptions<FieldListOptionsArray>
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
