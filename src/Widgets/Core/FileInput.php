<?php

declare(strict_types=1);

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Renders a file input chooser field.
 *
 * @extends Widget<mixed>
 */
class FileInput extends Widget
{
  public function __invoke(Field $field, array $options = []): string
  {
    $options = array_merge(['id' => $field->id], $options);
    $options['name'] = $field->name;
    $options['type'] = 'file';

    return sprintf('<input %s>', html_params($options));
  }
}
