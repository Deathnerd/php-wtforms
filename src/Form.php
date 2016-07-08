<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/31/2015
 * Time: 11:12 AM
 */

namespace WTForms;

use WTForms\Fields\Core\Field;
use WTForms\Fields\Core\Label;

/**
 * Declarative Form base class.
 * @package WTForms
 * @property array $data
 * @property array $errors
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
     * The CSRF implementation for this form
     * @var object
     */
    public $csrf;

    /**
     * Will hold the translations class when the
     * translations are in place
     * @var object
     */
    public $translations;

    /**
     * Implements the Arrayable interface for the form
     */
    use FormArrayable;

    /**
     * Implements the Iterable interface for the form
     */
    use FormIterator;

    /**
     * Initialize a form. This is usually done in the context of a view/controller in your application. When a form is
     * constructed, the fields populate their input based on the form
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
            if (!property_exists($csrf_field->csrf_impl, 'form_meta') || !$csrf_field->csrf_impl->form_meta) {
                $csrf_field->csrf_impl->form_meta = $this->meta;
            }
            $this->__set($csrf_name, $csrf_field);
        }

        if (array_key_exists('prefix', $options)) {
            $this->prefix = $options['prefix'];
        }
        // Normalize the end of the prefix to contain only one dash
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
            if (!$field->validate($this, $this->getExtraValidatorFor($name))) {
                $success = false;
            }
        }

        return $success && count($this->_errors) === 0;
    }

    /**
     * Extracts the names of extra validators defined on the `Form`. Searches for
     * functions with the pattern `validate_$name` where `$name` is the field name
     * defined on the `Form` object.
     *
     * @param $name string The name of the field to get an extra validator for
     *
     * @return array The names of extra validators defined on this form to be called
     *                by the field when executing validation
     */
    private function getExtraValidatorFor($name)
    {
        return method_exists($this, "validate_$name") ? [[$this, "validate_$name"]] : [];
    }

    /**
     * @internal
     */
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
     * @internal
     *
     * @param string $name  The name of the field to add to the current fields
     * @param mixed  $value The field object to add
     */
    public function __set($name, $value)
    {
        // Setting up a new field
        if ($value instanceof Field) {
            // Check for form object reference. This is needed for
            // accessing meta objects and other global objects
            if (!$value->form) {
                $value->form = $this;
            }
            // Make sure the prefix for the field is proper. These
            // are essential for Form Fields and Field Lists
            if (!$value->prefix) {
                $value->prefix = $this->prefix;
            }

            if (!$value->short_name) {
                $value->short_name = $name;
            }
            $value->name = $value->prefix . $name;
            $value->id = $value->name;
            if (!$value->label->text) {
                $value->label = new Label($value->id, ucwords(str_replace("_", " ", $value->short_name)));
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
     * @throws \InvalidArgumentException If something besides an object or an array were passed
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
        throw new \InvalidArgumentException(
            sprintf("Form::populate accepts only an array or an object as input; %s given.", gettype($data))
        );
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

    public function process(array $options = ["formdata" => [], "data" => []])
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
        // array
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

    /**
     * A convenience method to get around having to manually call the {@link process} function after form
     * instantiation. All this does internally is instantiate a new instance of the form with the given options,
     * processes those options, and returns the form ready to go.
     *
     * @param array $options The options to be passed to the constructor of the form
     *                       and the process function
     *
     * @return static The form all ready to go!
     */
    public static function create(array $options = [])
    {
        $form = new static($options);
        $form->process($options);

        return $form;
    }
}
