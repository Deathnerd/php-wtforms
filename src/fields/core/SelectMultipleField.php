<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 11:15 AM
 */

namespace WTForms\Fields\Core;


use WTForms\Form;
use WTForms\ValueError;
use WTForms\Widgets\Core\Select;

/**
 * No different from a normal select field, except this one can take (and
 * validate) multiple choices. You'll need to specify the HTML `size`
 * attribute to the select field when rendering
 *
 * @package WTForms\Fields
 */
class SelectMultipleField extends SelectField
{
  /**
   * @var array
   */
  public $data;

  public function __construct(array $options = ['choices' => []], Form $form = null)
  {
    parent::__construct($options, $form);
    $this->widget = array_key_exists('widget', $options) ? $options['widget'] : new Select();
    $this->widget->multiple = true;
  }

  /**
   * Provides data for choice widget rendering. Must return a sequence or
   * iterable of `[value,label,selected]` tuples
   * @return \Generator
   */
  public function getChoices()
  {
    foreach ($this->choices as $value => $label) {
      $selected = $this->data !== null && in_array($value, $this->data);
      yield ["value" => $value, "label" => $label, "selected" => $selected];
    }
  }

  /**
   * @param array|null $value
   */
  public function processData($value)
  {
    if ($value) {
      foreach ($value as $v) {
        $this->data[] = strval($v);
      }
    }
  }

  public function processFormData(array $valuelist)
  {
    if ($valuelist) {
      foreach ($valuelist as $v) {
        $this->data[] = strval($v);
      }
    }
  }

  public function preValidate(Form $form)
  {
    if ($this->data !== null) {
      $values = [];
      foreach ($this->choices as $c) {
        $values[] = $c[0];
      }
      foreach ($this->data as $d) {
        if (!in_array($d, $values)) {
          throw new ValueError("'$d' is not a valid choice for this field");
        }
      }
    }
  }
}
