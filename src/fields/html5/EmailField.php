<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:27 PM.
 */

namespace WTForms\Fields\HTML5;

use WTForms\Fields\Core\StringField;
use WTForms\Widgets\HTML5\EmailInput;

/**
 * Represents an ``<input type="email">``.
 */
class EmailField extends StringField
{
  public function __construct(array $options = [])
  {
    $options = array_merge(['widget' => new EmailInput()], $options);
    parent::__construct($options);
  }
}
