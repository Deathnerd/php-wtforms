<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 2:25 PM
 */

namespace WTForms\Fields\Core;

use WTForms\Form;
use WTForms\ValueError;
use WTForms\Widgets;
use WTForms\Widgets\Core\Option;
use WTForms\Widgets\Core\Select;

class SelectField extends SelectFieldBase
{
  /**
   * @var Option
   */
  public $option_widget;

  /**
   * @var array
   */
  public $choices = [];

  public function __construct($label = "", array $options = ['validators' => [], 'choices' => []])
  {
    $this->choices = $options['choices'];
    parent::__construct($label, $options);
    $this->widget = $options['widget'] ?: new Select();
    if(is_string($this->widget)){
      $w = $this->widget;
      $this->widget = new $w();
    }
  }

  public function getChoices()
  {
    $t = [];
    foreach ($this->choices as $x) {
      list($value, $label) = $x;
      $t[] = ["value" => $value, "label" => $label, "selected" => $value == $this->data];
    }

    return $t;
  }

  public function processData($value)
  {
    $this->data = $value;
  }

  public function processFormData(array $valuelist = [])
  {
    if (count($valuelist) != 0) {
      $this->data = $valuelist[0];
    }
  }

  public function preValidate(Form $form)
  {
    foreach ($this->choices as $v => $_) {
      if ($this->data == $v) {
        return;
      }
    }

    throw new ValueError("Not a valid choice");
  }
}
