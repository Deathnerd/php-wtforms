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

  public function __construct(array $options = ['choices' => []], Form $form = null)
  {
    if (array_key_exists('choices', $options)) {
      $this->choices = $options['choices'];
      unset($options['choices']);
    } else {
      $this->choices = [];
    }
    parent::__construct($options, $form);

    if (array_key_exists('widget', $options)) {
      $this->widget = $options['widget'];
    } else {
      $this->widget = new Select();
    }
  }

  public function getChoices()
  {
    foreach ($this->choices as list($label, $value)) {
      yield [$value, $label, $value == $this->data];
    }
  }

  public function processData($value)
  {
    $this->data = $value;
  }

  public function processFormData(array $valuelist)
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
