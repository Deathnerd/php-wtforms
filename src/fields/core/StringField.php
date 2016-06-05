<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/20/2016
 * Time: 10:02 PM
 */

namespace WTForms\Fields\Core;

use WTForms\Form;
use WTForms\Widgets;
use WTForms\Widgets\Core\TextInput;

/**
 * This field is the base for most of the more complicated fields, and
 * represents an ``<input type="text">``.
 * @package WTForms\Fields
 */
class StringField extends Field
{
  /**
   * @var TextInput
   */
  public $widget;

  public function __construct(array $options = [], Form $form = null)
  {
    if (!$this->widget) {
      $this->widget = new TextInput();
    }
    parent::__construct($options, $form);
  }

  /**
   * @param array $valuelist
   */
  public function processFormData(array $valuelist)
  {
    if ($valuelist) {
      $this->data = $valuelist[0];
    } else {
      $this->data = '';
    }
  }

  public function __get($name)
  {
    if ($name == "value") {
      return $this->data ?: "";
    }

    return null;
  }
}
