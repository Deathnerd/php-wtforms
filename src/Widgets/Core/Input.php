<?php

declare(strict_types=1);

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Render a basic ``<input>`` field.
 *
 * This is used as the basis for most other input fields.
 *
 * By default, the `_value()` method will be called upon the associated field
 * to provide the ``value=`` HTML attribute.
 *
 * @template TData
 *
 * @extends Widget<TData>
 */
class Input extends Widget
{
  public string $type;

  /**
   * @param string $type If passed, will add ``type="$input_type"`` to the
   *                     HTML attributes for this input
   */
  public function __construct(string $type = '')
  {
    if ('' !== $type) {
      $this->type = $type;
    }
  }

  public function __invoke(Field $field, array $options = []): string
  {
    $defaults = [
      'id'    => $field->id,
      'type'  => $this->type,
      'value' => $field->value,
    ];
    $options = array_merge($defaults, $options);
    $options['name'] = $field->name;

    return sprintf('<input %s>', html_params($options));
  }
}
