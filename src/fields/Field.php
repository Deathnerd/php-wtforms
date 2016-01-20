<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/4/2016
 * Time: 9:55 AM
 */

namespace Deathnerd\WTForms\Fields;

use CanCall;
use Deathnerd\WTForms\DummyTranslations;
use Deathnerd\WTForms\Widgets\Widget;

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

    public $name = '';

    public $flags = null;

    /**
     * Field constructor.
     * @param string $label
     * @param array $kwargs
     * @throws \TypeError
     */
    public function __construct($label = '', array $kwargs = [])
    {
        // TODO: Translations
        if (!is_null($kwargs['_meta'])) {
            $this->_meta = $kwargs['_meta'];
        } else if (!is_null($kwargs['_form'])) {
            $this->_meta = $kwargs['_form']->meta;
        } else {
            throw new \TypeError("Must provide one of _form or _meta");
        }
        $this->_translations = $kwargs['_translations'];
        $this->default = $kwargs['default'];
        $this->render_kw = is_null($kwargs['render_kw']) ? [] : $kwargs['render_kw'];

        $this->name = $kwargs['_prefix'] . $kwargs['_name'];
        $this->id = is_null($kwargs['id']) ? $this->name : $kwargs['id'];
        $this->description = $kwargs['description'];
        $this->flags = new Flags();
        $this->short_name = $kwargs['_name'];
        $this->type = gettype($this);
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

    public function call($kwargs = [])
    {
        return $this->_meta->render_field($this, $kwargs);
    }

    public function _value(){
        return "";
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

class Label extends CanCall
{

    /**
     * @var string
     */
    public $field_id = "";

    /**
     * @var string
     */
    public $text = "";

    /**
     * Label constructor.
     * @param string $field_id
     * @param string $text
     */
    public function __construct($field_id, $text)
    {
        $this->field_id = $field_id;
        $this->text = $text;
    }

    public function toHTML($text = null, $kwargs = [])
    {
        if(array_key_exists("for_", $kwargs)){
            $kwargs['for'] = $kwargs['for_'];
        } else {
            $kwargs['for'] = $this->field_id;
        }
    }
}
