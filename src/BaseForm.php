<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/21/2016
 * Time: 2:24 PM
 */

namespace Deathnerd\WTForms;


use Deathnerd\WTForms\Fields\Field;
use Deathnerd\WTForms\Fields\UnboundField;
use Illuminate\Support\Collection;

/**
 * Class BaseForm
 * Provides core behaviour like field construction, validation, and data and error proxying
 * @package Deathnerd\WTForms
 */
class BaseForm implements \Iterator
{
    use BaseFormIterator;
    /**
     * @var array
     */
    public $_fields;
    /**
     * @var string
     */
    public $_prefix;
    /**
     * @var DefaultMeta
     */
    public $meta;
    /**
     * @var array
     */
    public $_errors;
    /**
     * @var string
     */
    public $_csrf;

    /**
     * BaseForm constructor.
     * @param array $fields
     * @param string $prefix
     * @param DefaultMeta|null $meta
     */
    public function __construct($fields, $prefix = "", DefaultMeta $meta = null)
    {
        if ($prefix != "" && !preg_match("/\\-_\\;:\\/\\./", substr($prefix, -1))) {
            $prefix .= "-";
        }
        $this->meta = (is_null($meta)) ? new DefaultMeta() : $meta;
        $this->_prefix = $prefix;
        $this->_errors = [];
        $this->_fields = [];

        if (property_exists($fields, 'items')) {
            $fields = $fields->items();
        }

        $translations = $this->_get_translations();
        $extra_fields = [];

        if (property_exists($meta, 'csrf') && isset($meta->csrf)) {
            $this->_csrf = $meta->build_csrf($this);
            array_merge($extra_fields, $this->_csrf->setup_form($this));
        }

        foreach (chain($fields, $extra_fields) as $name => $unbound_field) {
            $options = ["name" => $name, "prefix" => $prefix, "translations" => $translations];
            $field = $meta->bind_field($this, $unbound_field, Collection::make($options));
            $this->_fields[$name] = $field;
        }
    }

    public function __get($field_name)
    {
        /*
         * Emulate the `form.data` and `form.errors` calls you can do in the original
         * Python implementation
         */
        if ($field_name == "data") {
            $ret_val = [];
            foreach ($this->_fields as $name => $field) {
                $ret_val[$name] = $field->data;
            }
            return $ret_val;
        } elseif ($field_name == "errors") {
            if (count($this->errors) == 0) {
                foreach ($this->_fields as $name => $field) {
                    if (count($field->errors) > 0) {
                        $this->_errors[$name] = $field->errors;
                    }
                }
            }
            return $this->_errors;
        }
        // Return the field as a property
        return $this->_fields[$field_name];
    }

    /**
     * Allow setting a field on the form as a property
     * @param string $field_name The name of the field to add to the current fields
     * @param UnboundField|Field $value The field object to add
     */
    public function __set($field_name, $value)
    {
        $this->_fields[$field_name] = $value->bind($this, $field_name, $this->_prefix);
    }

    /**
     *
     * @param string $field_name The name of the field to unset
     */
    public function __unset($field_name)
    {
        unset($this->_fields[$field_name]);
    }

    /**
     * Override in subclasses to provide alternate translations factory.
     *
     * must return an object that provides `gettext()` and `ngettext()` methods.
     * @deprecated 2.0 Use `Meta::get_translations` instead
     * @return mixed
     */
    private function _get_translations()
    {
        return $this->meta->get_translations($this);
    }

    /**
     * Populates the attributes of the passed `$obj` with data from the form's
     * fields.
     * NOTE: This is a destructive operation; any attribute with the same name
     * as a field will be overridden. Use with caution.
     * @param $obj
     */
    public function populate_obj($obj)
    {
        foreach ($this->_fields as $name => $f) {
            /** @var $field Field  */
            $field->populate_obj($obj, $name);
        }
    }

    /**
     * Take form, object data, and keyword arg input and have the fields
     * process them.
     *
     * @param array $formdata Used to pass data coming from the enduser, usually `$_POST` or equivalent
     * @param object|null $obj If `formdata` is empty, this object is checked for attributes matching
     * form field names, which will be used for field values.
     * @param array $data If provided, must be an associative array of data. This is only used if
     * `formdata` is empty and `obj` does not contain an attribute named the same as the field
     * @param array $kwargs If `formdata` is empty and `obj` does not contain an attribute named
     * the same as a field, form will assign the value of a matching keyword argument to the field,
     * if one exists.
     */
    public function process(array $formdata = [], $obj = null, array $data = [], array $kwargs = [])
    {
        $formdata = $this->meta->wrap_formdata($formdata);
        $kwargs = array_merge($data, $kwargs);

        foreach ($this->_fields as $name => $field) {
            /** @var $field Field|UnboundField */
            if (!is_null($obj) && property_exists($obj, $name)) {
                $field->process($formdata, $obj->$name);
            } elseif (array_key_exists($name, $kwargs)) {
                $field->process($formdata, $kwargs[$name]);
            } else {
                $field->process($formdata);
            }
        }
    }

    /**
     * Validates the form by calling the `validate` on each field.
     *
     * @param array $extra_validators If provided, is an associative array mapping field names to a
     * sequence of callables which will be passed as extra validators to the field's `validate` method
     * @return bool True if no errors occur, false if otherwise
     */
    public function validate(array $extra_validators = [])
    {
        $this->_errors = [];
        $success = true;
        foreach ($this->_fields as $name => $field) {
            /** @var $field Field|UnboundField */
            if (count($extra_validators) > 0 && in_array($name, $extra_validators)) {
                $extra = $extra_validators[$name];
            } else {
                $extra = [];
            }
            if (!$field->validate($this, $extra)) {
                $success = false;
            }
        }
        return $success;
    }

}