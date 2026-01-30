<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/31/2015
 * Time: 11:12 AM.
 */

namespace WTForms;

use ArrayAccess;
use InvalidArgumentException;
use Iterator;
use WTForms\Fields\Core\Field;
use WTForms\Fields\Core\Label;
use WTForms\Validators\Validator;

/**
 * Declarative Form base class.
 *
 * @property array $data
 * @property array $errors
 * @phpstan-consistent-constructor
 */
class Form implements ArrayAccess, Iterator {
  /**
   * Implements the Arrayable interface for the form.
   */
  use FormArrayable;

  /**
   * Implements the Iterable interface for the form.
   */
  use FormIterator;

  /**
   * @var array|Field|null
   */
  public $csrf_token;
  /**
   * If this is set, every field of this form
   * will have their name prefixed with this value
   * e.g. "foo-bar".
   *
   * @var string
   */
  public $prefix = '';

  /**
   * The input fields on the form.
   *
   * @var array<Field>
   */
  public $fields = [];

  /**
   * The meta object used for binding fields,
   * interacting with widgets, etc.
   *
   * @var DefaultMeta|null
   */
  public ?DefaultMeta $meta = null;

  /**
   * The CSRF implementation for this form.
   *
   * @var CSRF\Session\SessionCSRF|object
   */
  public $csrf;

  /**
   * Will hold the translations class when the
   * translations are in place.
   *
   * @TODO: Do the translations feature and figure out the actual class type we'll use here
   */
  public object $translations;

  /**
   * Cached errors from validation.
   */
  protected array $_errors = [];

  /**
   * Initialize a form. This is usually done in the context of a view/controller in your application. When a form is
   * constructed, the fields populate their input based on the form.
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
      [$csrf_name, $csrf_field] = $this->csrf->setupForm($this);
      if (!property_exists($csrf_field->csrf_impl, 'form_meta') || !$csrf_field->csrf_impl->form_meta) {
        $csrf_field->csrf_impl->form_meta = $this->meta;
      }
      $this->__set($csrf_name, $csrf_field);
    }

    if (array_key_exists('prefix', $options)) {
      $this->prefix = $options['prefix'];
    }
    // Normalize the end of the prefix to contain only one dash
    if ($this->prefix && !str_contains('-_;:/.', substr($this->prefix, -1))) {
      $this->prefix .= '-';
    }
  }

  /**
   * Validates the form by calling `validate` on each field, passing any
   * extra `Form.validate_<fieldname>` validators to the field validator.
   */
  public function validate(): bool
  {
    $this->_errors = [];
    $success = true;
    foreach ($this->fields as $name => $field) {
      if (!$field->validate($this, ...$this->getExtraValidatorFor($name))) {
        $success = false;
      }
    }

    return $success && 0 === count($this->errors);
  }

  /**
   * Extracts the names of extra validators defined on the `Form`. Searches for
   * functions with the pattern `validate_$name` where `$name` is the field name
   * defined on the `Form` object.
   *
   * @param $name string The name of the field to get an extra validator for
   *
   * @return array<Validator|callable> The names of extra validators defined on this form to be called
   *               by the field when executing validation
   */
  private function getExtraValidatorFor(string $name): array
  {
    if (method_exists($this, "validate_$name")) {
      return [
          $this->{"validate_$name"}(...)
      ];
    }
    return [];
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
    if ('data' == $field_name) {
      return array_map(fn($field) => $field->data, $this->fields);
    } elseif ('errors' == $field_name) {
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
   * Allow setting a field on the form as a property.
   *
   * @param string $name  The name of the field to add to the current fields
   * @param mixed  $value The field object to add
   *
   * @internal
   */
  public function __set(string $name, $value)
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
      if ($value->label instanceof Label) {
        if (!$value->label->text) {
          $value->label = new Label($value->id, ucwords(str_replace('_', ' ', $value->short_name)));
        }
      } elseif (is_string($value->label) && $value->label !== '') {
        $value->label = new Label($value->id, $value->label);
      } else {
        $value->label = new Label($value->id, ucwords(str_replace('_', ' ', $value->short_name)));
      }
      $this->fields[$name] = $value;
      // Also set the property directly if it exists (e.g., csrf_token)
      // so that accessing $form->csrf_token returns the field, not null
      if (property_exists($this, $name)) {
        $this->$name = $value;
      }
    } elseif (array_key_exists($name, $this->fields)) {
      $this->fields[$name]->data = $value;
    } else {
      $this->$name = $value;
    }
  }

  /**
   * @param string $name The name of the field to unset
   */
  public function __unset(string $name)
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
   * @template TArray of array
   * @template T of object
   *
   * @param TArray|T $data
   *
   * @return TArray|T The result of populating the object or array passed
   *
   * @throws InvalidArgumentException If something besides an object or an array were passed
   */
  public function populate($data)
  {
    if (is_object($data)) {
      return $this->populateObj($data);
    }
    if (is_array($data)) {
      return $this->populateArray($data);
    }
    throw new InvalidArgumentException(sprintf('Form::populate accepts only an array or an object as input; %s given.', gettype($data)));
  }

  /**
   * This takes in an object with properties and assigns their
   * values to the values of fields with the same name on this form.
   *
   * This is a destructive operation. If a field exists on this form
   * that has the same name as a property on the object, it WILL BE OVERWRITTEN.
   *
   * @template T of object
   *
   * @param T $obj The object to populate
   *
   * @return T The object with the data from the form replacing values for members of the object
   */
  public function populateObj(object $obj): object
  {
    foreach ($this->fields as $field_name => $field) {
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
   * @template TArray of array
   *
   * @param TArray $array The array to populate
   *
   * @return TArray The array with data from the form replacing values already existing in the array
   */
  public function populateArray(array $array): array
  {
    foreach ($this->fields as $field_name => $field) {
      if ($field->data) {
        $array[$field_name] = $field->data;
      }
    }

    return $array;
  }

  /**
   * Creates a new instance of the given class and populates it with form data.
   *
   * This is the preferred method for type-safe population of DTOs/entities.
   *
   * @template T of object
   *
   * @param class-string<T> $class The fully-qualified class name to instantiate
   *
   * @return T The populated object
   */
  public function populateTo(string $class): object
  {
    $obj = new $class();
    foreach ($this->fields as $field_name => $field) {
      $field->populateObj($obj, $field_name);
    }

    return $obj;
  }

  /**
   * Process form data and populate field values.
   *
   * Takes submitted form data and/or default values and processes them
   * through each field. Fields receive data in this priority order:
   * 1. Object properties (if 'obj' is provided and has matching property)
   * 2. Named options (e.g., $options['username'] for a 'username' field)
   * 3. Data array values (if 'data' is provided with matching key)
   * 4. Formdata (raw form submission data, typically $_POST)
   *
   * @template T of object
   *
   * @param array{
   *     formdata?: array<string, mixed>,
   *     obj?: T|null,
   *     data?: array<string, mixed>,
   *     ...
   * } $options Processing options:
   *            - formdata: Raw form submission data (e.g., $_POST). Keys are field names.
   *            - obj: Object with properties matching field names for default values.
   *            - data: Array of default values keyed by field name.
   *            - Additional keys are treated as individual field default values and
   *            take precedence over values in the 'data' array.
   *
   * @return void
   */
  public function process(array $options = ['formdata' => [], 'data' => []])
  {
    if (array_key_exists('formdata', $options)) {
      $formdata = $options['formdata'];
      unset($options['formdata']);
    } else {
      $formdata = [];
    }

    if (array_key_exists('obj', $options)) {
      $obj = $options['obj'];
      unset($options['obj']);
    } else {
      $obj = null;
    }

    if (array_key_exists('data', $options)) {
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
  public static function create(array $options = []): Form
  {
    $form = new static($options);
    $form->process($options);

    return $form;
  }
}
