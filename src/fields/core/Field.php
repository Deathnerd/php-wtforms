<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/4/2016
 * Time: 9:55 AM
 */

namespace WTForms\Fields\Core;

use Symfony\Component\OptionsResolver\OptionsResolver;
use WTForms\DefaultMeta;
use WTForms\Form;
use WTForms\Validators\StopValidation;
use WTForms\Validators\Validator;
use WTForms\ValueError;
use WTForms\Widgets\Core\Widget;

/**
 * Field base class
 * @property  boolean $checked
 * @package WTForms\Fields
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
   * @var null
   */
  public $raw_data = null;
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
  public $render_kw;
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
   * Field constructor.
   *
   * @param string $label
   * @param array  $kwargs
   *
   * @throws \TypeError
   */
  public function __construct($label = '', array $kwargs = [])
  {
    $resolver = new OptionsResolver();
    $resolver->setDefaults([
        "validators"  => [],
        "filters"     => [],
        "description" => "",
        "id"          => null,
        "default"     => null,
        "widget"      => null,
        "render_kw"   => null,
        "form"        => null,
        "name"        => null,
        "prefix"      => '',
        "meta"        => null,
        "attributes"  => [],
      /// Inherited from annotation but not needed so here to keep OptionsResolver happy
        "class"       => null,
        "classes"     => null,
        "inherited"   => null,
        "method"      => null,
        "multiple"    => null,
        "property"    => null,
        "type"        => null,
        "label"       => null,
        "value"       => null,
    ]);
    $kwargs = $resolver->resolve($kwargs);


    if (array_key_exists('meta', $kwargs) && !is_null($kwargs['meta'])) {
      $this->meta = $kwargs['meta'];
    } else if (array_key_exists('form', $kwargs) && !is_null($kwargs['form'])) {
      $this->meta = $kwargs['form']->meta;
    } else {
      $this->meta = new DefaultMeta();
    }
    $this->default = $kwargs['default'];
    $this->description = $kwargs['description'];
    $this->render_kw = $kwargs['render_kw'];
    $this->filters = $kwargs['filters'];
    $this->flags = new Flags();
    $this->name = $kwargs['prefix'] . $kwargs['name'];
    $this->short_name = $kwargs['name'];
    $this->type = get_class();
    $this->validators = $kwargs['validators'];
    $this->id = is_null($kwargs['id']) ? $this->name : $kwargs['id'];
    $this->label = new Label($this->id, $label != "" ? $label : ucwords(str_replace("_", " ", $kwargs['name'])));
    $this->widget = $kwargs['widget'];
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
   * @param array $kwargs
   *
   * @return mixed
   */
  public function __invoke($kwargs = [])
  {
    return $this->meta->render_field($this, $kwargs);
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
      $this->pre_validate($form);
    } catch (StopValidation $e) {
      if (!empty($e->args) && $e->args[0]) {
        $this->errors[] = $e->getMessage();
      }
      $stop_validation = true;
    } catch (ValueError $e) {
      $this->errors[] = $e->getMessage();
    }

    if (!$stop_validation) {
      $stop_validation = $this->_run_validation_chain($form, $this->validators) && $this->_run_validation_chain($form, $extra_validators);
    }

    // Call post_validate
    try {
      $this->post_validate($form, $stop_validation);
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
  public function pre_validate(Form $form)
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
  private function _run_validation_chain(Form $form, array $validators)
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
  public function post_validate(Form $form, $stop_validation)
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
      $this->process_data($data);
    } catch (ValueError $e) {
      $this->process_errors[] = $e->getMessage();
    }

    if ($formdata) {
      try {
        if (in_array($this->name, $formdata)) {
          $this->raw_data = $_REQUEST[$this->name];
        } else {
          $this->raw_data = [];
        }
        $this->process_formdata($this->raw_data);
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
   * This will be called during form construction by the form's `kwargs` or
   * `obj` argument.
   *
   * @param string|array $value
   */
  public function process_data($value)
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
  public function process_formdata(array $valuelist)
  {
    if (count($valuelist) > 0) {
      $this->data = $valuelist[0];
    }
  }

  /**
   * To satisfy my static analyzer
   * @return string
   */
  public function _value()
  {
    return "";
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
  public function populate_obj($obj, $name)
  {
    $obj->$name = $this->data;
  }

  /**
   * Runs final initialization procedures. Checks for things such as Meta references
   * and holding form references
   *
   * @param Form        $form
   * @param Widget|null $widget
   * @param array       $validators
   */
  public function finalize(Form $form, $widget, array $validators = [])
  {
    $this->form = $form;
    $this->validators = $validators;
    $t = $this->validators;
    $t[] = $this->widget;
    foreach ($t as $v) {
      foreach ($v->field_flags as $flag) {
        $this->flags->$flag = true;
      }
    }
    if ($widget) {
      $this->widget = $widget;
    }
    if (!$this->meta && $form->meta) {
      $this->meta = $form->meta;
    }
    $this->name = $form->prefix . $this->name;
  }
}
