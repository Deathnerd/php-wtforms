<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/21/2016
 * Time: 10:58 AM
 */

namespace Deathnerd\WTForms\Fields;


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
            return $this->$name;
        }
        return false;
    }

    public function __set($name, $value)
    {
        if (substr($name, 0, 1) != "_") {
            $name = "_$name";
        }
        $this->$name = $value;
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
