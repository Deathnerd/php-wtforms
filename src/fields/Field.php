<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/4/2016
 * Time: 9:55 AM
 */

namespace Deathnerd\WTForms\Fields;

use CanCall;
use Deathnerd\WTForms\DefaultMeta;
use Deathnerd\WTForms\DummyTranslations;
use Deathnerd\WTForms\Form;
use Deathnerd\WTForms\StopValidation;
use Deathnerd\WTForms\Utils;
use Deathnerd\WTForms\Validator;
use Deathnerd\WTForms\Widgets\Widget;

define('UNSET_VALUE', new Utils\UnsetValue());

/**
 * Field base class
 * @package Deathnerd\WTForms\Fields
 */
class Field extends CanCall implements \Iterator
{
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
     * @var array
     */
    public $validators = [];
    /**
     * @var Widget
     */
    public $widget = null;
    /**
     * @var array
     */
    public $entries = [];
    public $description;
    public $default;
    /**
     * @var array
     */
    public $render_kw;
    /**
     * @var string
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
     * @var bool
     */
    protected $_formField = true;
    /**
     * @var DummyTranslations
     */
    protected $_translations;
    /**
     * @var bool In original source, used for Django 1.4 Traversal. Kept here for... reasons
     */
    public $do_not_call_in_templates = true;

    /**
     * @var string
     */
    public $id = '';

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var Flags
     */
    public $flags;

    /**
     * @var Label
     */
    public $label;

    /**
     * Field constructor.
     * @param string $label
     * @param array $kwargs
     * @throws \TypeError
     */
    public function __construct($label = '', array $kwargs = [])
    {
        // TODO: Translations
        if (!is_null($kwargs['_translations'])) {
            $this->_translations = $kwargs['_translations'];
        } else {
            $this->_translations = new DummyTranslations();
        }
        if (!is_null($kwargs['_meta'])) {
            $this->meta = $kwargs['_meta'];
        } else if (!is_null($kwargs['_form'])) {
            $this->meta = $kwargs['_form']->meta;
        } else {
            throw new \TypeError("Must provide one of _form or _meta");
        }
        $this->_translations = $kwargs['_translations'];
        $this->default = $kwargs['default'];
        $this->description = $kwargs['description'];
        $this->render_kw = array_key_exists("render_kw", $kwargs) ? [] : $kwargs['render_kw'];
        $this->filters = $kwargs['filters'];
        $this->flags = new Flags();
        $this->name = $kwargs['_prefix'] . $kwargs['_name'];
        $this->short_name = $kwargs['_name'];
        $this->type = get_class();
        $this->validators = array_key_exists("validators", $kwargs) ? $kwargs['validators'] : $this->validators;

        $this->id = is_null($kwargs['id']) ? $this->name : $kwargs['id'];
        $this->type = gettype($this);
        // TODO: Translations
        $this->label = new Label($this->id, array_key_exists("label", $kwargs) ? $kwargs['label'] : str_replace("_", " ", $kwargs['_name']));

        if (!is_null($kwargs['widget'])) {
            $this->widget = $kwargs['widget'];
        }
    }

    /**
     * Render this field as HTML, using keyword args as additional attributes
     *
     * This delegates rendering to {@link Deathnerd\WTForms\DefaultMeta\render_field}
     * whose default behavior is to call the field's widget, passing any
     * keyword arguments from this call to the widget.
     *
     * In all of WTForms HTML widgets, the keyword arguments are turned to
     * HTML attributes, though in theory a widget is free to do anything it
     * wants with the supplied keyword arguments, and widgets don't have to
     * even do anything related to HTML
     *
     * @param array $kwargs
     * @return mixed
     */
    public function __invoke($kwargs = [])
    {
        return $this->meta->render_field($this, $kwargs);
    }

    /**
     * Get a translation for the given message.
     *
     * This proxies for the internal translations object
     *
     * @param string $string A string to be translated
     * @return mixed A string which is the translated output
     */
    public function gettext($string)
    {
        return $this->_translations->gettext($string);
    }

    /**
     * Get a translation for a message which can be pluralized.
     *
     * @param string $singular The singular form of the message
     * @param string $plural The plural form of the message
     * @param int $n The number of elements this message is referring to
     * @return mixed A string which is the translated output
     */
    public function ngettext($singular, $plural, $n)
    {
        return $this->_translations->ngettext($singular, $plural, $n);
    }

    /**
     * Validates the field and returns true or false. {@link errors} will
     * contain any errors raised during validation. This is usually only
     * called by {@link Form\validate}
     *
     * Subfields shouldn't override this, but rather override either
     * {@link pre_validate}, {@link post_validate}, or both, depending on needs.
     *
     * @param Form $form The form the field belongs to.
     * @param array $extra_validators A sequence of extra validators to run
     * @return bool
     */
    public function validate(Form $form, array $extra_validators = [])
    {
        $this->errors = $this->process_errors;
        $stop_validation = false;

        // Call pre-validate
        try {
            $this->pre_validate($form);
        } catch (StopValidation $e) {
            if (!empty($e->args) && $e->args[0]) {
                $this->errors[] = $e->args[0];
            }
            $stop_validation = true;
        } // TODO: ValueErrors

        if (!$stop_validation) {
            $chain = chain([$this->validators, $extra_validators]);
            $stop_validation = $this->_run_validation_chain($form, $chain);
        }

        // Call post_validate
        // TODO: ValueErrors
        $this->post_validate($form, $stop_validation);

        return count($this->errors) == 0;
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
     * @param $formdata
     * @param Utils\UnsetValue|mixed $data
     */
    public function process($formdata, $data = UNSET_VALUE)
    {
        $this->process_errors = [];
        if(is_a($data, 'UnsetValue')){

        }
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return Field Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return current($this->entries);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return Field Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        return next($this->entries);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->entries);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        $key = key($this->entries);
        $var = ($key !== null && $key !== false);
        return $var;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->entries);
    }

    public function _value()
    {
        return "";
    }


    /**
     * Run a validation chain, stopping if any validator raises StopValidation
     *
     * @param Form $form The form instance this field belongs to
     * @param \Generator $validators A sequence or iterable of validator callables
     * @return bool True if the validation was stopped, False if otherwise
     */
    private function _run_validation_chain(Form $form, \Generator $validators)
    {
        foreach ($validators as $v) {
            /**
             * @var Validator
             */
            $validator = $v;
            try {
                $validator->call($form, $this);
            } catch (StopValidation $e) {
                if (!empty($e->args) && $e->args[0]) {
                    $this->errors[] = $e->args[0];
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Override if you need field-level validation. Runs before any other
     * validators.
     * @param Form $form The form the field belongs to
     */
    private function pre_validate($form)
    {
    }

    /**
     * Override if you need to run any field-level validation tasks after
     * normal validation. This shouldn't be needed in most cases
     *
     * @param Form $form The form the field belongs to
     * @param boolean $stop_validation `True` if any validator raised `StopValidation`
     */
    private function post_validate($form, $stop_validation)
    {
    }
}

/**
 * Holds a set of boolean flags as attributes
 *
 * Accessing a non-existing attribute returns false for its value
 * @package Deathnerd\WTForms\Fields
 */
class Flags
{
    public function __get($name)
    {
        if (substr($name, 0, 1) == "_" && property_exists($this, $name)) {
            return $this->{$name};
        }
        return false;
    }

    public function __set($name, $value)
    {
        if (substr($name, 0, 1) != "_") {
            $name = "_$name";
        }
        $this->{$name} = $value;
    }

    function __toString()
    {
        $flags = [];
        foreach (get_object_vars($this) as $name => $value) {
            if (substr($name, 0, 1) == "_") {
                $flags[] = "$name: " . strval($value);
            }
        }
        return '<wtforms.fields.Flags: {' . implode(", ", $flags) . '}>';
    }
}
