<?php

declare(strict_types=1);

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Renders a multi-line text area.
 *
 * `rows` and `cols` ought to be passed as members of the `$options` array when
 * rendering.
 *
 * @extends Widget<string>
 */
class TextArea extends Widget
{
  public function __invoke(Field $field, array $options = []): string
  {
    $options = array_merge(['id' => $field->id], $options);
    $options['name'] = $field->name;

    return sprintf('<textarea %s>%s</textarea>', html_params($options), e($field->value));
  }
}
