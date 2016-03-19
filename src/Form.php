<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/31/2015
 * Time: 11:12 AM
 */

namespace WTForms;

use WTForms\Fields\Core\Field;


/**
 * Class Form
 * @package WTForms
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
   *
   * @param Field[]          $fields
   * @param string           $prefix
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
    $this->errors = [];
    foreach ($this->fields as $name => $field) {
      /** @var $field Field */
      $field->validate($this);
    }

    return count($this->errors) === 0;
  }


  public function __get($field_name)
  {
    /*
     * Emulate the `form.data` and `form.errors` calls you can do in the original
     * Python implementation
     */
    if ($field_name == "data") {
      $ret_val = [];
      foreach ($this->fields as $name => $field) {
        $ret_val[$name] = $field->data;
      }

      return $ret_val;
    } elseif ($field_name == "errors") {
      if ($this->errors) {
        foreach ($this->fields as $name => $field) {
          if ($field->errors) {
            $this->errors[$name] = $field->errors;
          }
        }
      }

      return $this->errors;
    }

    // Return the field as a property
    return $this->fields[$field_name];
  }

  /**
   * Allow setting a field on the form as a property
   *
   * @param string $field_name The name of the field to add to the current fields
   * @param Field  $field      The field object to add
   */
  public function __set($field_name, Field $field)
  {
    $this->fields[$field_name] = $field;
  }

  /**
   *
   * @param string $field_name The name of the field to unset
   */
  public function __unset($field_name)
  {
    unset($this->fields[$field_name]);
  }

  /**
   * Convenience method for populating objects and arrays.
   * Under the hood it calls {@link populateObj} or {@link populateArray}
   * depending on what was passed into the function.
   *
   * @param array|object $thing
   *
   * @throws \RuntimeException If something besides an object or an array were passed
   * @return array|object The result of populating the object or array passed
   */
  public function populate($thing)
  {
    if (is_object($thing)) {
      return $this->populateObj($thing);
    }
    if (is_array($thing)) {
      return $this->populateArray($thing);
    }
    throw new \RuntimeException(sprintf("Form::populate accepts only an array or an object as input; %s given.", gettype($thing)));
  }

  /**
   * This takes in an object with properties and assigns their
   * values to the values of fields with the same name on this form.
   *
   * This is a destructive operation. If a field exists on this form
   * that has the same name as a property on the object, it WILL BE OVERWRITTEN.
   *
   * @param object $obj The object to populate
   *
   * @return object The object with the data from the form replacing values for members of the object
   */
  public function populateObj($obj)
  {
    foreach ($this->fields as $field_name => $field) {
      if (property_exists($obj, $field_name) && $field->data) {
        $obj->$field_name = $field->data;
      }
    }

    return $obj;
  }

  /**
   * This takes in a keyed array and assigns their values to
   * the values of fields with the same name as the key.
   *
   * !!!!!!!!!!!!!
   * !!!WARNING!!!
   * !!!!!!!!!!!!!
   * This is a destructive operation. If a field exists on this form
   * that has the same name as a key in the array, it WILL BE OVERWRITTEN.
   *
   * @param array $array The array to populate
   *
   * @return array The array with data from the form replacing values already existing in the array
   */
  public function populateArray(array $array)
  {
    foreach ($this->fields as $field_name => $field) {
      if ($field->data) {
        $array[$field_name] = $field->data;
      }
    }

    return $array;
  }
}
