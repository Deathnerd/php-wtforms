<?php

declare(strict_types=1);

namespace WTForms\Fields\Options;

use WTForms\Exceptions\TypeError;
use WTForms\Fields\Core\Field;
use WTForms\Fields\Core\FieldList;

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

  /**
   * @throws TypeError
   */
  public function validate(): void
  {
    if (is_null($this->inner_field)) {
      throw new TypeError('FieldList requires an inner_field declaration');
    }
    if (!$this->inner_field instanceof Field) {
      $type = is_object($this->inner_field) ? get_class($this->inner_field) : gettype($this->inner_field);
      throw new TypeError(sprintf('FieldList requires an inner_field type subclassing Field; %s given', $type));
    }
    if ($this->filters) {
      throw new TypeError('FieldList does not accept any filters. Instead, define them on the enclosed field');
    }
  }

  /**
   * Pure property assignment from validated options to target object.
   *
   * @param FieldList $obj
   *
   * @return FieldList
   *
   * @throws TypeError
   */
  public function hydrateObject(object $obj): object
  {
    $this->validate();

    $obj->inner_field = $this->inner_field;
    $obj->min_entries = $this->min_entries;
    $obj->max_entries = $this->max_entries;

    return $obj;
  }
}
