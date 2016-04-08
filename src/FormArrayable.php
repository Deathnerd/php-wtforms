<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/6/2016
 * Time: 4:23 PM
 */

namespace WTForms;

/**
 * @property $fields
 */
trait FormArrayable
{
  public function offsetExists($offset)
  {
    return array_key_exists($offset, $this->fields);
  }

  public function offsetGet($offset)
  {
    if (array_key_exists($offset, $this->fields)) {
      return $this->fields[$offset];
    }
    return null;
  }

  public function offsetSet($offset, $value)
  {
    $this->fields[$offset] = $value;
    $this->$offset = $value;
  }

  public function offsetUnset($offset)
  {
    unset($this->fields[$offset]);
    $this->$offset = null;
  }
}