<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/4/2016
 * Time: 9:55 AM.
 */

namespace WTForms\Fields\Core;

use AllowDynamicProperties;
use BadMethodCallException;
use Closure;
use Iterator;
use WTForms\DefaultMeta;
use WTForms\Exceptions\StopValidation;
use WTForms\Exceptions\ValidationError;
use WTForms\Exceptions\ValueError;
use WTForms\Fields\Options\FieldOptions;
use WTForms\Flags;
use WTForms\Form;
use WTForms\Validators\Validator;
use WTForms\Widgets\Core\Widget;

/**
 * Field base class.
 *
 * @template TData
 *
 * @phpstan-import-type FieldOptionsArray from FieldOptions
 *
 * @property bool               $checked
 * @property TData              $data
 * @property string|null        $value
 * @property array<int, string> $errors
 * @uses FieldIterator<TData>
 *
 * @method string label(array $options = [])
 */
#[AllowDynamicProperties]
class Field implements Iterator
{
  use FieldIterator;

  public ?Form $form = null;

  /**
   * @var array<int, string>
   */
  public array $process_errors = [];

  /**
   * @var array<int, mixed>|null
   */
  public ?array $raw_data = [];

  /**
   * @var list<Validator|callable>
   */
  public array $validators = [];

  /**
   * The widget for rendering this field.
   */
  public Widget|null $widget = null;

  /**
   * @var array<int, mixed>
   */
  public array $entries = [];

  public string $description = '';

  /**
   * Default value - can be a callable that returns the default or the value itself.
   *
   * @var callable|mixed
   */
  public $default;

  /**
   * @var array<string, mixed>
   */
  public array $render_kw = [];

  /**
   * @var list<callable>
   */
  public array $filters = [];

  public string $short_name = '';

  public string $type = '';

  public DefaultMeta $meta;

  /**
   * The original object data passed to the field.
   */
  public $object_data;

  public string $id = '';

  public string $name = '';

  public Flags $flags;
  public Label|string|null $label = null;

  /**
   * @var array<string, mixed>
   */
  public array $attributes = [];

  public ?string $prefix = null;

  /**
   * Options passed to constructor, used for cloning fields in FieldList.
   *
   * @phpstan-var FieldOptionsArray
   */
  public array $constructor_options = [];

  /**
   * Field constructor.
   *
   * @phpstan-param FieldOptionsArray|FieldOptions $options
   * @deprecated Passing an array is deprecated. Use a FieldOptions instance instead.
   */
  public function __construct(array|FieldOptions $options = [])
  {
    if ($options instanceof FieldOptions) {
      $this->constructFromOptions($options);
      return;
    }

    if (array_key_exists('form', $options)) {
      $this->form = $options['form'];
      unset($options['form']);
    } else {
      $this->form = $this->resolveParentForm();
    }
    /**
     * @phpstan-var FieldOptionsArray&array<string, mixed> $options
     */
    $options = array_merge([
      'validators'  => [],
      'filters'     => [],
      'description' => '',
      'id'          => null,
      'default'     => null,
      'widget'      => null,
      'render_kw'   => [],
      'name'        => null,
      'prefix'      => '',
      'meta'        => new DefaultMeta(),
      'attributes'  => [],
      'label'       => null,
      'class'       => null,
    ], $options);

    $this->constructor_options = $options;

    if (!is_null($this->form) && property_exists($this->form, 'meta') && $this->form->meta instanceof DefaultMeta) {
      $this->meta = $this->form->meta;
    } elseif (!is_null($options['meta'])) {
      $this->meta = $options['meta'];
    }
    // Always unset meta
    unset($options['meta']);

    $this->render_kw = array_merge(array_merge($this->render_kw, $options['attributes']), $options['render_kw']);
    unset($options['attributes']);
    unset($options['render_kw']);
    if ($options['class']) {
      $this->render_kw['class'] = $options['class'];
    }
    unset($options['class']);

    $this->default = $options['default'];
    unset($options['default']);
    $this->description = $options['description'];
    unset($options['description']);
    $this->filters = $options['filters'];
    unset($options['filters']);
    $this->flags = new Flags();
    if ($options['prefix']) {
      $prefix = $options['prefix'];
    } elseif (!is_null($this->form)) {
      $prefix = $this->form->prefix;
    } else {
      $prefix = '';
    }
    unset($options['prefix']);
    $this->name = $prefix.($options['name'] ?? '');
    $this->short_name = $options['name'] ?? '';
    unset($options['name']);
    $this->type = get_class();
    $this->validators = $options['validators'];
    unset($options['validators']);
    if (is_null($options['id'])) {
      $this->id = $this->name;
    } else {
      $this->id = $options['id'];
    }
    unset($options['id']);
    $label = $options['label'] ?? ucwords(str_replace('_', ' ', $this->short_name));
    unset($options['label']);
    $this->label = new Label($this->id, $label);
    if ($options['widget']) {
      $this->widget = $options['widget'];
    }
    unset($options['widget']);
    $t = [];
    foreach ($this->validators as $v) {
      $t[] = $v;
    }
    if ($this->widget instanceof Widget) {
      $t[] = $this->widget;
    }
    foreach ($t as $x) {
      if (is_object($x) && property_exists($x, 'field_flags') && $x->field_flags) {
        foreach ($x->field_flags as $flag) {
          $this->flags->set($flag);
        }
      }
    }
    // If there are options left over, treat them as render keywords
    if ($options) {
      $this->render_kw = array_merge($this->render_kw, $options);
    }
  }

  /**
   * Construct the field from a typed FieldOptions object, bypassing
   * the array-based construction path.
   */
  protected function constructFromOptions(FieldOptions $options): void
  {
    $this->constructor_options = $options->toArray();

    // Resolve form
    $this->form = $options->form ?? $this->resolveParentForm();

    // Resolve meta: prefer form's meta, then options, then default
    if ($this->form?->meta instanceof DefaultMeta) {
      $this->meta = $this->form->meta;
    } elseif ($options->meta !== null) {
      $this->meta = $options->meta;
    } else {
      $this->meta = new DefaultMeta();
    }

    // Merge render keywords: attributes + render_kw + class
    $this->render_kw = array_merge($this->render_kw, $options->attributes, $options->render_kw);
    if ($options->class) {
      $this->render_kw['class'] = $options->class;
    }

    // Simple property assignments
    $this->default = $options->default;
    $this->description = $options->description;
    $this->filters = $options->filters;
    $this->validators = $options->validators;
    $this->flags = new Flags();

    // Resolve prefix and name
    $prefix = $options->prefix ?: ($this->form?->prefix ?? '');
    $this->name = $prefix . ($options->name ?? '');
    $this->short_name = $options->name ?? '';
    $this->id = $options->id ?? $this->name;
    $this->type = get_class();

    $labelText = $options->label ?? ucwords(str_replace('_', ' ', $this->short_name));
    $this->label = new Label($this->id, $labelText);

    // Widget
    if ($options->widget) {
      $this->widget = $options->widget;
    }

    // Collect field flags from validators and widget
    $this->collectFieldFlags();

    // Let subclasses run post-construction derived state
    $this->postHydrate();
  }

  /**
   * Collect field_flags from validators and widget, setting them on $this->flags.
   */
  protected function collectFieldFlags(): void
  {
    $sources = $this->validators;
    if ($this->widget instanceof Widget) {
      $sources[] = $this->widget;
    }
    foreach ($sources as $source) {
      if (is_object($source) && property_exists($source, 'field_flags') && $source->field_flags) {
        foreach ($source->field_flags as $flag) {
          $this->flags->set($flag);
        }
      }
    }
  }

  /**
   * Hook for subclasses to run post-construction logic after options
   * have been applied. Use this for derived state like default widgets,
   * label promotion, or computed properties.
   *
   * Called at the end of the FieldOptions-based construction path.
   */
  protected function postHydrate(): void
  {
    // Base implementation is a no-op.
  }

  public function __clone()
  {
    if ($this->default instanceof Closure) {
      $this->default = $this->default->bindTo($this);
    }
  }

  public function __toString(): string
  {
    return $this->__invoke();
  }

  /**
   * Render this field as HTML, using keyword args as additional attributes.
   *
   * This delegates rendering to {@link DefaultMeta}
   * whose default behavior is to call the field's widget, passing any
   * keyword arguments from this call to the widget.
   *
   * In all of WTForms HTML widgets, the keyword arguments are turned to
   * HTML attributes, though in theory a widget is free to do anything it
   * wants with the supplied keyword arguments, and widgets don't have to
   * even do anything related to HTML
   *
   * @param array<string, mixed> $options
   */
  public function __invoke(array $options = []): string
  {
    return $this->meta->renderField($this, $options);
  }

  /**
   * @param array<int, mixed> $arguments
   */
  public function __call(string $name, array $arguments)
  {
    if ('label' == $name) {
      if (is_string($this->label)) {
        $this->label = new Label($this->id, $this->label);
      }
      if (count($arguments) > 0) {
        return $this->label->__invoke($arguments[0]);
      }

      return $this->label->__invoke();
    }

    throw new BadMethodCallException("Method $name not found on form!");
  }

  /**
   * Validates the field and returns true or false. {@link errors} will
   * contain any errors raised during validation. This is usually only
   * called by {@link Form::validate()}.
   *
   * Subfields shouldn't override this, but rather override either
   * {@link pre_validate}, {@link post_validate}, or both, depending on needs.
   *
   * @param Form               $form                the form the field belongs to
   * @param callable|Validator ...$extra_validators A sequence of extra validators to run
   * @noinspection PhpRedundantCatchClauseInspection Extended classes may throw in their own preValidate method
   */
  public function validate(Form $form, Validator|callable ...$extra_validators): bool
  {
    $this->errors = $this->process_errors;
    $stop_validation = false;

    // Call pre-validate
    try {
      $this->preValidate($form);
    } catch (StopValidation $e) {
      $message = $e->getMessage();
      if ('' != $message) {
        $this->errors[] = $message;
      }
      $stop_validation = true;
    } catch (ValueError|ValidationError $e) {
      $this->errors[] = $e->getMessage();
    }

    if (!$stop_validation) {
      $stop_validation = $this->runValidationChain($form, array_merge($this->validators, $extra_validators));
    }

    // Call post_validate
    try {
      $this->postValidate($form, $stop_validation);
    } catch (ValueError $e) {
      $this->errors[] = $e->getMessage();
    }

    return 0 == count($this->errors);
  }

  /**
   * Override if you need field-level validation. Runs before any other
   * validators.
   *
   * @param Form $form The form the field belongs to
   */
  public function preValidate(Form $form): void
  {
  }

  /**
   * Run a validation chain, stopping if any validator raises StopValidation.
   *
   * @param Form                     $form       The form instance this field belongs to
   * @param list<Validator|callable> $validators A sequence or iterable of validator callables
   *
   * @return bool True if the validation was stopped, False if otherwise
   */
  protected function runValidationChain(Form $form, array $validators): bool
  {
    foreach ($validators as $v) {
      try {
        if (is_array($v)) {
          call_user_func($v, $form, $this);
        } else {
          $v($form, $this);
        }
      } catch (StopValidation|ValidationError|ValueError $e) {
        $message = $e->getMessage();
        if ('' != $message) {
          $this->errors[] = $message;
        }

        return true;
      }
    }

    return false;
  }

  /**
   * Override if you need to run any field-level validation tasks after
   * normal validation. This shouldn't be needed in most cases.
   *
   * @param Form $form            The form the field belongs to
   * @param bool $stop_validation `True` if any validator raised `StopValidation`
   */
  public function postValidate(Form $form, bool $stop_validation): void
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
   * @param array<string, mixed> $formdata
   * @param TData|null           $data
   * @noinspection PhpRedundantCatchClauseInspection Extended classes may throw in their own processData or processFormData methods
   */
  public function process(array $formdata, mixed $data = null): void
  {
    $this->process_errors = [];
    if (null === $data) {
      if (is_callable($this->default)) {
        $data = ($this->default)();
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

    try {
      foreach ($this->filters as $filter) {
        /* @var callable $filter */
        $this->data = $filter($data);
      }
    } catch (ValueError $e) {
      $this->process_errors[] = $e->getMessage();
    }
  }

  /**
   * Process the data applied to this field and store the result.
   *
   * This will be called during form construction by the form's `options` or
   * `obj` argument.
   *
   * @param TData $value
   */
  public function processData($value): void
  {
    $this->data = $value;
  }

  /**
   * Process data received over the wire from a form.
   *
   * This will be called during form construction with data supplied
   * through the `formdata` argument
   *
   * @param array<int, string> $valuelist A list of strings to process
   */
  public function processFormData(array $valuelist): void
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
   * @template T of object
   *
   * @param T $obj
   */
  public function populateObj(object $obj, string $name): void
  {
    $obj->$name = $this->data;
  }

  /**
   * Magic getter method to retrieve specific properties.
   *
   * @param string $name The name of the property being accessed
   *
   * @return mixed The value of the requested property or null if the property does not exist
   */
  public function __get(string $name)
  {
    if ('value' == $name) {
      if ($this->raw_data) {
        return $this->raw_data[0];
      } elseif (null !== $this->data) {
        return strval($this->data);
      }

      return '';
    }

    return null;
  }

  /**
   * Resolves and returns the first parent instance of the Form class
   * found in the debug backtrace.
   *
   * @return Form|null The parent Form instance if found, or null if none exists
   */
  private function resolveParentForm(): ?Form
  {
    $backtrace = array_reverse(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 4));
    while ($stack = array_pop($backtrace)) {
      if ($stack['object'] instanceof Form) {
        return $stack['object'];
      }
    }

    return null;
  }
}
