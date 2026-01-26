<?php

declare(strict_types=1);

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;
use WTForms\Fields\Core\SelectFieldBase;

/**
 * Renders a select field.
 *
 * If `$multiple` is true, then the `size` property should be specified on rendering
 * to make the field useful.
 *
 * The field must provide a `getChoices()` method which the widget will
 * call on rendering; this method must yield tuples of
 * `($value, $label, $selected)`.
 *
 * @extends Widget<string|int>
 */
class Select extends Widget
{
  public bool $multiple = false;

  /**
   * @param SelectFieldBase<string|int> $field
   */
  public function __invoke(Field $field, array $options = []): string
  {
    $options = array_merge(['id' => $field->id], $options);
    $options['name'] = $field->name;
    if ($this->multiple) {
      $options['multiple'] = true;
    }

    /** @var SelectFieldBase<string|int> $field */
    $html = sprintf('<select %s>', html_params($options));
    foreach ($field->getChoices() as [$value, $label, $selected]) {
      $html .= self::renderOption($value, $label, $selected);
    }
    $html .= '</select>';

    return $html;
  }

  /**
   * Render a single option element.
   *
   * @param string|int|bool      $value
   * @param array<string, mixed> $options
   */
  public static function renderOption($value, string $label, bool $selected, array $options = []): string
  {
    if (true === $value) {
      // Handle the special case of a true value
      $value = 'true';
    }
    $options = array_merge($options, ['value' => $value]);
    if ($selected) {
      $options['selected'] = true;
    }

    return sprintf('<option %s>%s</option>', html_params($options), $label);
  }
}
