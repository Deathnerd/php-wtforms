<?php

declare(strict_types=1);

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;
use WTForms\Fields\Core\SelectFieldBase;
use WTForms\Widgets\Options\ListWidgetOptions;

/**
 * Renders a list of fields as `ul` or `ol` list.
 *
 * This is used for fields which encapsulate many inner fields as subfields.
 * The widget will try to iterate the field to get access to the subfields and
 * call them to render them.
 *
 * If `$prefix_label` is set, the subfield's label is printed before the field,
 * otherwise afterwards. The latter is useful for iterating radios or
 * checkboxes.
 *
 * @extends Widget<mixed>
 */
class ListWidget extends Widget
{
  public string $html_tag = '';

  /**
   * If set to true, subfields will have their labels printed before them,
   * otherwise afterwards.
   */
  public bool $prefix_label = true;

  /**
   * @param string $html_tag     Must be 'ul' or 'ol'
   * @param bool   $prefix_label If true, subfield labels appear before the field
   */
  public function __construct(string|ListWidgetOptions $html_tag = 'ul', bool $prefix_label = true)
  {
    if ($html_tag instanceof ListWidgetOptions) {
      $this->constructFromOptions($html_tag);
      return;
    }
    assert(in_array($html_tag, ['ul', 'ol']));
    $this->html_tag = $html_tag;
    $this->prefix_label = $prefix_label;
  }

  public function __invoke(Field $field, array $options = []): string
  {
    $options = array_merge(['id' => $field->id], $options);
    $html = sprintf('<%s %s>', $this->html_tag, html_params($options));
    if ($field instanceof SelectFieldBase) {
      $field = $field->options;
    }
    foreach ($field as $subfield) {
      if ($this->prefix_label) {
        $html .= sprintf('<li>%s %s</li>', $subfield->label, $subfield());
      } else {
        $html .= sprintf('<li>%s %s</li>', $subfield(), $subfield->label);
      }
    }
    $html .= sprintf('</%s>', $this->html_tag);

    return $html;
  }
}
