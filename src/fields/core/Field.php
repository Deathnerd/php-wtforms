<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/4/2016
 * Time: 9:55 AM
 */

namespace WTForms\Fields\Core;

use WTForms\DefaultMeta;
use WTForms\Flags;
use WTForms\Form;
use WTForms\Validators\StopValidation;
use WTForms\Validators\ValidationError;
use WTForms\Validators\Validator;
use WTForms\ValueError;
use WTForms\Widgets\Core\Widget;

/**
 * Field base class
 * @property  boolean    $checked
 * @package WTForms\Fields
 * @property null|string value
 */
class Field implements \Iterator
{
  use FieldIterator;

  /**
   * @var Form
   */
  public $form;
  /**
   * @var mixed
   */
  public $data;
  /**
   * @var array
   */
  public $errors = [];
  /**
   * @var array
   */
  public $process_errors = [];
  /**
   * @var array
   */
  public $raw_data = [];
  /**
   * @var Validator[]
   */
  public $validators = [];
  /**
   * @var Widget
   */
  public $widget;
  /**
   * @var array
   */
  public $entries = [];
  /**
   * @var string
   */
  public $description;
  /**
   * @var callable|mixed
   */
  public $default;
  /**
   * @var array
   */
  public $render_kw = [];
  /**
   * @var array
   */
  public $filters;
  /**
   * @var string
   */
  public $short_name;
  /**
   * @var string
   */
  public $type;
  /**
   * @var DefaultMeta
   */
  public $meta;
  /**
   * @var mixed
   */
  public $object_data;
  /**
   * @var string
   */
  public $id = '';
  /**
   * @var string
   */
  public $name = '';
  /**
   * @var Flags[]
   */
  public $flags;
  /**
   * @var Label
   */
  public $label;
  /**
   * @var array
   */
  public $attributes;
  /**
   * @var string|null
   */
  public $prefix;

  /**
   * Field constructor.
   *
   * @param string $label
   * @param array  $options
   *
   * @throws \TypeError
   */
  public function __construct($label = '', array $options = [])
  {
    $options = array_merge([
        "validators"  => [],
        "filters"     => [],
        "description" => "",
        "id"          => null,
        "default"     => null,
        "widget"      => null,
        "render_kw"   => [],
        "form"        => null,
        "name"        => null,
        "prefix"      => '',
        "meta"        => new DefaultMeta(),
        "attributes"  => [],
        "label"       => null,
        "class"       => null,
        "class_"      => [],
        "class__"     => [],
        "value"       => null,
    ], $options);

    if (array_key_exists('meta', $options) && !is_null($options['meta'])) {
      $this->meta = $options['meta'];
    } else if (array_key_exists('form', $options) && !is_null($options['form'])) {
      $this->meta = $options['form']->meta;
    }
    if (is_string($this->meta)) {
      $this->meta = new $this->meta();
    }

    if ($options['class_']) {
      $this->render_kw['class'] = $options['class_'];
    } elseif ($options['class__']) {
      $this->render_kw['class'] = $options['class__'];
    }
    $this->render_kw = array_merge(array_merge($this->render_kw, $options['attributes']), $options['render_kw']);

    $this->default = $options['default'];
    $this->description = $options['description'];
    $this->filters = $options['filters'];
    $this->flags = new Flags();
    $this->name = $options['prefix'] . $options['name'];
    $this->short_name = $options['name'];
    $this->type = get_class();
    $this->validators = $options['validators'];
    $this->id = is_null($options['id']) ? $this->name : $options['id'];
    $this->label = new Label($this->id, $label !== null ? $label : ucwords(str_replace("_", " ", $options['name'])));
    $w = $options['widget'];
    if ($w) {
      if (is_object($w)) {
        $this->widget = $w;
      } else {
        $this->widget = new $w();
      }
    }

    $t = $this->validators;
    $t[] = $this->widget;
    foreach ($t as $v) {
      if ($v->field_flags) {
        foreach ($v->field_flags as $flag) {
          $this->flags->$flag = true;
        }
      }
    }
  }

  public function __toString()
  {
    return (string)$this->__invoke();
  }

  /**
   * Render this field as HTML, using keyword args as additional attributes
   *
   * This delegates rendering to {@link WTForms\DefaultMeta\render_field}
   * whose default behavior is to call the field's widget, passing any
   * keyword arguments from this call to the widget.
   *
   * In all of WTForms HTML widgets, the keyword arguments are turned to
   * HTML attributes, though in theory a widget is free to do anything it
   * wants with the supplied keyword arguments, and widgets don't have to
   * even do anything related to HTML
   *
   * @param array $options
   *
   * @return mixed
   */
  public function __invoke($options = [])
  {
    return $this->meta->render_field($this, $options);
  }

  public function __call($name, $arguments)
  {
    if ($name == "label") {
      if (count($arguments) == 2) {
        return $this->label->__invoke($arguments[0], $arguments[1]);
      } elseif (count($arguments) == 1) {
        return $this->label->__invoke($arguments[0]);
      }

      return $this->label->__invoke();
    }

    throw new \BadMethodCallException("Method $name not found on form!");
  }

  /**
   * Validates the field and returns true or false. {@link errors} will
   * contain any errors raised during validation. This is usually only
   * called by {@link Form\validate}
   *
   * Subfields shouldn't override this, but rather override either
   * {@link pre_validate}, {@link post_validate}, or both, depending on needs.
   *
   * @param Form        $form             The form the field belongs to.
   * @param Validator[] $extra_validators A sequence of extra validators to run
   *
   * @return bool
   */
  public function validate($form, array $extra_validators = [])
  {
    $this->errors = $this->process_errors;
    $stop_validation = false;

    // Call pre-validate
    try {
      $this->preValidate($form);
    } catch (StopValidation $e) {
      if (!empty($e->args) && $e->args[0]) {
        $this->errors[] = $e->getMessage();
      }
      $stop_validation = true;
    } catch (ValueError $e) {
      $this->errors[] = $e->getMessage();
    }

    if (!$stop_validation) {
      $stop_validation = $this->runValidationChain($form, $this->validators) && $this->runValidationChain($form, $extra_validators);
    }

    // Call post_validate
    try {
      $this->postValidate($form, $stop_validation);
    } catch (ValueError $e) {
      $this->errors[] = $e->getMessage();
    }

    return count($this->errors) == 0;
  }

  /**
   * Override if you need field-level validation. Runs before any other
   * validators.
   *
   * @param Form $form The form the field belongs to
   */
  public function preValidate(Form $form)
  {
  }

  /**
   * Run a validation chain, stopping if any validator raises StopValidation
   *
   * @param Form        $form       The form instance this field belongs to
   * @param Validator[] $validators A sequence or iterable of validator callables
   *
   * @return bool True if the validation was stopped, False if otherwise
   * @throws \WTForms\NotImplemented
   */
  protected function runValidationChain(Form $form, array $validators)
  {
    foreach ($validators as $v) {
      try {
        $v->__invoke($form, $this);
      } catch (StopValidation $e) {
        $message = $e->getMessage();
        if ($message != "") {
          $form->errors[] = $message;
        }

        return true;
      } catch (ValidationError $e) {
        $message = $e->getMessage();
        if ($message != "") {
          $form->errors[] = $message;
        }

        return true;
      }
    }

    return false;
  }

  /**
   * Override if you need to run any field-level validation tasks after
   * normal validation. This shouldn't be needed in most cases
   *
   * @param Form    $form            The form the field belongs to
   * @param boolean $stop_validation `True` if any validator raised `StopValidation`
   */
  public function postValidate(Form $form, $stop_validation)
  {
  }

  /**
   * Process incoming data, calling process_data, process_formdata as needed,
   * and run filters.
   *
   * If `data` is not provided, process_data will be called on the field's
   * default.
   *
   * Field subclasses usually won't override this, instead overriding the
   * process_formdata and process_data methods. Only override this for
   * special advanced processing, such as when a field encapsulates many
   * inputs.
   *
   * @param            $formdata
   * @param null|mixed $data
   */
  public function process($formdata, $data = null)
  {
    $this->process_errors = [];
    if ($data === null) {
      // Should work... See example at: http://php.net/manual/en/language.oop5.magic.php#object.invoke
      if (is_callable($this->default)) {
        $data = $this->default->__invoke();
      } else {
        $data = $this->default;
      }
    }
    $this->object_data = $data;
    try {
      $this->processData($data);
    } catch (ValueError $e) {
      $this->process_errors[] = $e->getMessage();
    }

    if ($formdata) {
      try {
        if (array_key_exists($this->name, $formdata)) {
          if (is_array($formdata[$this->name])) {
            $this->raw_data = $formdata[$this->name];
          } else {
            $this->raw_data = [$formdata[$this->name]];
          }
        } else {
          $this->raw_data = [];
        }
        $this->processFormData($this->raw_data);
      } catch (ValueError $e) {
        $this->process_errors[] = $e->getMessage();
      }
    }

    foreach ($this->filters as $filter) {
      /** @var $filter \WTForms\Interfaces\FilterInterface */
      $this->data = $filter::run($data);
    }
  }

  /**
   * Process the data applied to this field and store the result.
   *
   * This will be called during form construction by the form's `options` or
   * `obj` argument.
   *
   * @param string|array $value
   */
  public function processData($value)
  {
    $this->data = $value;
  }

  /**
   * Process data received over the wire from a form.
   *
   * This will be called during form construction with data supplied
   * through the `formdata` argument
   *
   * @param array $valuelist A list of strings to process
   */
  public function processFormData(array $valuelist)
  {
    if (count($valuelist) > 0) {
      $this->data = $valuelist[0];
    }
  }

  /**
   * Populates `$obj->$name` with the field's data.
   *
   * Note: This is a destructive operation. If `$obj->$name` already exists,
   * it will be overridden. Use with caution
   *
   * @param $obj
   * @param $name
   */
  public function populateObj($obj, $name)
  {
    $obj->$name = $this->data;
  }

  public function __get($name)
  {
    if (in_array($name, ["value"])) {
      if ($this->raw_data) {
        return $this->raw_data[0];
      } elseif ($this->data !== null) {
        return strval($this->data);
      }

      return "";
    }

    return null;
  }


}
