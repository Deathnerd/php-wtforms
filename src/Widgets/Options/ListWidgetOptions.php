<?php

declare(strict_types=1);

namespace WTForms\Widgets\Options;

use WTForms\Internal\Types\ArrayOptions;

/**
 * Options for the ListWidget.
 *
 * @phpstan-type ListWidgetOptionsArray array{html_tag?: string, prefix_label?: bool}
 * @extends ArrayOptions<ListWidgetOptionsArray>
 */
class ListWidgetOptions extends ArrayOptions
{
  public string $html_tag = 'ul';
  public bool $prefix_label = true;

  public function hydrateObject(object $obj): object
  {
    $obj->html_tag = $this->html_tag;
    $obj->prefix_label = $this->prefix_label;
    return $obj;
  }

  protected function getDefaults(): array
  {
    return ['html_tag' => 'ul', 'prefix_label' => true];
  }
}
