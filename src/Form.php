<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/31/2015
 * Time: 11:12 AM
 */

namespace Deathnerd\WTForms;

use Deathnerd\WTForms\Fields\Core\Field;
use Deathnerd\WTForms\Fields\Core\UnboundField;


/**
 * Class Form
 * @package Deathnerd\WTForms
 */
class Form implements \ArrayAccess, \Iterator
{
  public $prefix = "";
  public $fields = [];
  public $errors = [];
  public $meta;
  public $csrf;

  use FormArrayable;
  use FormIterator;

  /**
   * Form constructor.
   * @param Field[] $fields
   * @param string $prefix
   * @param DefaultMeta|null $meta
   */
  public function __construct(array $fields = [], $prefix = "", $meta = null)
  {
  }

  function __invoke()
  {
  }

  /**
   * Validates the form by calling `validate` on each field, passing any
   * extra `Form.validate_<fieldname>` validators to the field validator.
   * @return bool
   */
  public function validate()
  {
    $this->_errors = [];
    foreach ($this->fields as $name => $field) {
      /** @var $field Field */
      $field->validate($this);
    }
    return count($this->errors) === 0;
  }


  /**
   * Allow setting a field on the form as a property
   * @param string $field_name The name of the field to add to the current fields
   * @param UnboundField|Field $value The field object to add
   */
  public function __set($field_name, $value)
  {
  }

  /**
   * @param string $field_name The name of the field to unset
   */
  public function __unset($field_name)
  {
  }

  public function populateObj($obj){
    foreach($this->fields as $field_name=>$field){
      $obj->$field_name = $field->data;
    }
  }

  public function populateArray(array $array){
    return array_merge($this->fields, $array);
  }
}
