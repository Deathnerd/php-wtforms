<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 5:04 PM.
 */

namespace WTForms\Fields\Simple;

use WTForms\Fields\Core\BooleanField;
use WTForms\Widgets\Core\SubmitInput;

/**
 * Represents an ``<input type="submit">``.  This allows checking if a given
 * submit button has been pressed.
 */
class SubmitField extends BooleanField
{
  public function __construct(array $options = [])
  {
    $options = array_merge(['widget' => new SubmitInput()], $options);
    parent::__construct($options);
  }
}
