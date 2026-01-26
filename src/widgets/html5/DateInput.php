<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 5:18 PM.
 */

namespace WTForms\Widgets\HTML5;

use WTForms\Widgets\Core\Input;

/**
 * Renders an input with type "date".
 */
class DateInput extends Input
{
  public function __construct()
  {
    parent::__construct('date');
  }
}
