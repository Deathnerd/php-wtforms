<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/18/2016
 * Time: 3:46 PM.
 */

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Just so I can have type hinting for widgets.
 */
abstract class Widget
{
  public array $field_flags = [];

  /**
   * @param Field|mixed $field
   */
  abstract public function __invoke($field, array $options = []);
}
