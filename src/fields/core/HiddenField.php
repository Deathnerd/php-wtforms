<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/20/2016
 * Time: 10:02 PM
 */

namespace WTForms\Fields\Core;


use WTForms\Form;
use WTForms\Widgets\Core\HiddenInput;

class HiddenField extends StringField
{
  public function __construct(array $options = [], Form $form = null)
  {
    parent::__construct($options, $form);
    $this->widget = new HiddenInput();
  }
}
