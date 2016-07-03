<?php
/**
 * Beyond this line, lie dragons... Enter at your own risk
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/17/2016
 * Time: 2:46 PM
 */

namespace WTForms\Fields\Core;

use WTForms\Exceptions\TypeError;
use WTForms\Form;
use WTForms\Widgets\Core\TableWidget;

/**
 * Encapsulate a form as a field into another form
 * @package WTForms\Fields\Core
 */
class FormField extends Field implements \ArrayAccess
{

    public $separator = "-";
    public $form_class;
    private $obj;

    /**
     * @inheritdoc
     * @throws \TypeError
     */
    public function __construct(array $options = [])
    {
        if (!array_key_exists('form_class', $options)) {
            throw new TypeError("FormField must have a form_class property set!");
        }
        if (array_key_exists('filters', $options)) {
            throw new TypeError("FormField cannot take filters, as the encapsulated data is not mutable");
        }

        if (array_key_exists('validators', $options)) {
            throw new TypeError("FormField does not accept any validators. Instead, define them on the enclosed form.");
        }

        $this->form_class = $options['form_class'];
        unset($options['form_class']);
        if (array_key_exists('separator', $options)) {
            $this->separator = $options['separator'];
            unset($options['separator']);
        }
        $this->obj = null;

        $options = array_merge(["widget" => new TableWidget()], $options);
        parent::__construct($options);
    }

    /**
     * @inheritdoc
     */
    public function process($formdata, $data = null)
    {
        if (is_null($data) || !$data) {
            if (is_callable($this->default)) {
                $data = $this->default->__invoke();
            } else {
                $data = $this->default;
            }
            $this->obj = $data;
        }

        $this->object_data = $data;
        $prefix = $this->name . $this->separator;

        /**
         * @var $form_class Form
         */
        $form_class = $this->form_class;
        if (is_array($data)) {
            $this->form = $form_class::create([
                "prefix"   => $prefix,
                "formdata" => $formdata,
                "data"     => $data,
            ]);
        } else {
            $this->form = $form_class::create([
                "prefix"   => $prefix,
                "formdata" => $formdata,
                "obj"      => $data,
            ]);
        }
    }

    /**
     * @inheritdoc
     * @throws TypeError
     */
    public function validate(Form $form, array $extra_validators = [])
    {
        if ($extra_validators) {
            throw new TypeError('FormField does not accept in-line validators,' .
                ' as it gets errors from the enclosed form.');
        }

        return $this->form->validate();
    }

    /**
     * @inheritdoc
     */
    public function populateObj($obj, $name)
    {
        $candidate = $obj->$name;
        if (is_null($candidate)) {
            if (is_null($this->obj)) {
                throw new TypeError('populate_obj: cannot find a value to populate ' .
                    'from the provided obj or input data/defaults');
            }
            $candidate = $this->obj;
            $obj->$name = $candidate;
        }
        $this->form->populateObj($candidate);
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if (in_array($name, ["data", "errors"]) || property_exists($this->form, $name)) {
            return $this->form->$name;
        }
        if (array_key_exists($name, $this->form->fields)) {
            return $this->form[$name];
        }

        return parent::__get($name); // @codeCoverageIgnore
    }

    /**
     * @codeCoverageIgnore
     */
    public function current()
    {
        return current($this->form->fields);
    }

    /**
     * @codeCoverageIgnore
     */
    public function next()
    {
        next($this->form->fields);
    }

    /**
     * @codeCoverageIgnore
     */
    public function key()
    {
        return key($this->form->fields);
    }

    /**
     * @codeCoverageIgnore
     */
    public function valid()
    {
        $key = key($this->form->fields);

        return ($key !== null && $key !== false);
    }

    /**
     * @codeCoverageIgnore
     */
    public function rewind()
    {
        reset($this->form->fields);
    }

    /**
     * @codeCoverageIgnore
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->form->fields);
    }

    /**
     * @codeCoverageIgnore
     */
    public function offsetGet($offset)
    {
        if (array_key_exists($offset, $this->form->fields)) {
            return $this->form->fields[$offset];
        }

        return null;
    }

    /**
     * @codeCoverageIgnore
     */
    public function offsetSet($offset, $value)
    {
        $this->form->fields[$offset] = $value;
        $this->form->$offset = $value;
    }

    /**
     * @codeCoverageIgnore
     */
    public function offsetUnset($offset)
    {
        unset($this->form->fields[$offset]);
        $this->form->$offset = null;
    }

}