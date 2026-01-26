<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 5:26 PM.
 */

namespace WTForms\Widgets\HTML5;

use WTForms\Widgets\Core\Input;

/**
 * Renders an input with the type "color".
 */
class ColorInput extends Input
{
  public function __construct()
  {
    parent::__construct('color');
  }
}
