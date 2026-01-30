<?php

declare(strict_types=1);

namespace WTForms\Fields\Options;

/**
 * Options class for DateTimeField constructors.
 *
 * @phpstan-import-type FieldOptionsArray from FieldOptions
 * @phpstan-type DateTimeFieldOptionsArray array{format?: string} & FieldOptionsArray
 * @extends FieldOptions<DateTimeFieldOptionsArray>
 */
class DateTimeFieldOptions extends FieldOptions
{
  /**
   * The strftime-style format string for parsing/displaying dates.
   */
  public string $format = '%Y-%m-%d %H:%i:%s';

  /**
   * @param \WTForms\Fields\Core\DateTimeField $obj
   * @return \WTForms\Fields\Core\DateTimeField
   */
  public function hydrateObject(object $obj): object
  {
    parent::hydrateObject($obj);
    $obj->format = $this->format;
    return $obj;
  }

  protected function getDefaults(): array
  {
    return array_merge(parent::getDefaults(), [
      'format' => '%Y-%m-%d %H:%i:%s',
    ]);
  }
}
