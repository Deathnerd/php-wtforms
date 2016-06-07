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
 * @property array $data
 * @property array $errors
 * @property
 */
class Form implements \ArrayAccess, \Iterator
{
  /**
   * If this is set, every field of this form
   * will have their name prefixed with this value
   * e.g. "foo-bar"
   * @var string
   */
  public $prefix = "";
  /**
   * The input fields on the form
   * @var array<Field>
   */
  public $fields = [];
  /**
   * The meta object used for binding fields,
   * interacting with widgets, etc.
   * @var DefaultMeta
   */
  public $meta;
  /**
   * Will hold the CSRF validation implementation
   * when it is complete
   * @var object
   */
  public $csrf;
  /**
   * Will hold the translations class when the
   * translations are in place
   * @var object
   */
  public $translations;

  use FormArrayable;
  use FormIterator;

  /**
   * Form constructor.
   * TODO: Detail options for Form
   *
   * @param array $options
   */
  public function __construct(array $options = [])
  {
    // Update the meta values for this form's meta object with a user-supplied array
    // of key=>value pairs mapping to properties on this form's meta object
    if (array_key_exists('meta', $options) && is_array($options['meta'])) {
      $this->meta->updateValues($options['meta']);
    }

    // if CSRF is enabled on the Meta field, then generate a CSRF field
    // automagically and attach it to the form
    if ($this->meta && $this->meta->csrf) {
      $this->csrf = $this->meta->buildCSRF($this);
      list($csrf_name, $csrf_field) = $this->csrf->setupForm($this);
      if (!$csrf_field->csrf_impl->form_meta) {
        $csrf_field->csrf_impl->form_meta = $this->meta;
      }
      $this->__set($csrf_name, $csrf_field);
    }

    if (array_key_exists('prefix', $options)) {
      $this->prefix = $options['prefix'];
    }
    if ($this->prefix && !str_contains("-_;:/.", substr($this->prefix, -1))) {
      $this->prefix .= "-";
    }
  }

  /**
   * Validates the form by calling `validate` on each field, passing any
   * extra `Form.validate_<fieldname>` validators to the field validator.
   * @return bool
   */
  public function validate()
  {
    $this->_errors = [];
    $success = true;
    foreach ($this->fields as $name => $field) {
      /** @var $field Field */
      if (!$field->validate($this) || !$this->runExtraValidators($name)) {
        $success = false;
      }
    }

    return $success && count($this->_errors) === 0;
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
      if (!$this->_errors) {
        foreach ($this->fields as $name => $field) {
          if ($field->errors) {
            $this->_errors[$name] = $field->errors;
          }
        }
      }

      return $this->_errors;
    }

    // Return the field as a property
    return array_key_exists($field_name, $this->fields) ? $this->fields[$field_name] : null;
  }

  /**
   * Allow setting a field on the form as a property
   *
   * @param string $name  The name of the field to add to the current fields
   * @param mixed  $value The field object to add
   */
  public function __set($name, $value)
  {
    if ($value instanceof Field) {
      if (!$value->form) {
        $value->form = $this;
      }
      if (!$value->prefix) {
        $value->prefix = $this->prefix;
      }
      if (!$value->short_name) {
        $value->short_name = $name;
      }
      if (!$value->name) {
        $value->name = $value->prefix . $name;
      }
      if (!$value->id) {
        $value->id = $value->name;
      }
      $this->fields[$name] = $value;
    } elseif (array_key_exists($name, $this->fields)) {
      $this->fields[$name]->data = $value;
    } else {
      $this->$name = $value;
    }
  }

  /**
   * @param string $name The name of the field to unset
   */
  public function __unset($name)
  {
    if (array_key_exists($name, $this->fields)) {
      unset($this->fields[$name]);
    } else {
      unset($this->$name); // @codeCoverageIgnore
    }

  }

  /**
   * Convenience method for populating objects and arrays.
   * Under the hood it calls {@link populateObj} or {@link populateArray}
   * depending on what was passed into the function.
   *
   * @param array|object $data
   *
   * @throws \RuntimeException If something besides an object or an array were passed
   * @return array|object The result of populating the object or array passed
   */
  public function populate($data)
  {
    if (is_object($data)) {
      return $this->populateObj($data);
    }
    if (is_array($data)) {
      return $this->populateArray($data);
    }
    throw new \RuntimeException(sprintf("Form::populate accepts only an array or an object as input; %s given.", gettype($data))); //@codeCoverageIgnore
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
  public function populateObj(&$obj)
  {
    foreach ($this->fields as $field_name => $field) {
      /**
       * @var Field $field
       */
      $field->populateObj($obj, $field_name);
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

  public function process(array $options)
  {
    if (array_key_exists("formdata", $options)) {
      $formdata = $options['formdata'];
      unset($options['formdata']);
    } else {
      $formdata = [];
    }

    if (array_key_exists("obj", $options)) {
      $obj = $options['obj'];
      unset($options['obj']);
    } else {
      $obj = null;
    }

    if (array_key_exists("data", $options)) {
      $data = $options['data'];
      unset($options['data']);
    } else {
      $data = [];
    }

    // If a field's value was declared by name in the options
    // then it has precedence over its entry in the data
    // dictionary
    if ($data && is_array($data)) {
      $options = array_merge($data, $options);
    }

    foreach ($this->fields as $name => $field) {
      if ($obj && property_exists($obj, $name)) {
        $field->process($formdata, $obj->{$name});
      } elseif (array_key_exists($name, $options)) {
        $field->process($formdata, $options[$name]);
      } else {
        $field->process($formdata);
      }
    }
  }

  private function runExtraValidators($name)
  {
    if ((method_exists($this, "validate_$name") && !call_user_func([$this, "validate_$name"]))) {
      return false;
    }
    $uc_name = ucfirst($name);
    if ((method_exists($this, "validate$uc_name") && !call_user_func([$this, "validate$uc_name"]))) {
      return false;
    }

    return true;
  }
}
