<?php

declare(strict_types=1);

namespace WTForms\Widgets\Options;

use WTForms\Internal\Types\ArrayOptions;

/**
 * Options for the TableWidget.
 *
 * @phpstan-type TableWidgetOptionsArray array{with_table_tag?: bool}
 * @extends ArrayOptions<TableWidgetOptionsArray>
 */
class TableWidgetOptions extends ArrayOptions
{
  public bool $with_table_tag = true;

  public function hydrateObject(object $obj): object
  {
    $obj->with_table_tag = $this->with_table_tag;
    return $obj;
  }

  protected function getDefaults(): array
  {
    return ['with_table_tag' => true];
  }
}
