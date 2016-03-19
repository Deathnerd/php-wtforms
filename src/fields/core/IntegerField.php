<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 1:24 PM
 */

namespace WTForms\Fields\Core;

use WTForms\ValueError;
use WTForms\Widgets\Core\TextInput;

/**
 * A text field, except all input is coerced to an integer. Erroneous input
 * is ignored and will nto be accepted as a value
 *
 * @package WTForms\Fields
 */
class IntegerField extends Field
{
  /**
   * IntegerField constructor.
   *
   * @param string $label
   * @param array  $kwargs
   */
  public function __construct($label = "", array $kwargs = [])
  {
    parent::__construct($label, $kwargs);
    $this->widget = new TextInput();
  }

  public function _value()
  {
    if ($this->raw_data) {
      return $this->raw_data[0];
    } elseif ($this->raw_data !== null) {
      return strval($this->data);
    }

    return "";
  }

  public function process_formdata(array $valuelist)
  {
    if ($valuelist) {
      if (is_numeric($valuelist[0])) {
        $this->data = intval($valuelist[0]);
      } else {
        $this->data = null;
        throw new ValueError("Not a valid integer value");
      }
    }
  }
}
