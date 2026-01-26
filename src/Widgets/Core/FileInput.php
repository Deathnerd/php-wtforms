<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/3/2016
 * Time: 3:31 PM.
 */

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Renders a file input chooser field.
 */
class FileInput extends Widget
{
  /**
   * @param Field $field
   *
   * @return string
   */
  public function __invoke($field, array $options = [])
  {
    $options = array_merge(['id' => $field->id], $options);
    $options['name'] = $field->name;
    $options['type'] = 'file';

    return sprintf('<input %s>', html_params($options));
  }
}
