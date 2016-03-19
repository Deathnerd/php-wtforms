<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/21/2016
 * Time: 10:58 AM
 */

namespace WTForms\Fields\Core;


/**
 * Holds a set of boolean flags as attributes
 *
 * Accessing a non-existing attribute returns false for its value
 * @package WTForms\Fields
 */
class Flags
{
    public function __get($name)
    {
        if (starts_with($name, "_") && property_exists($this, $name)) {
            return $this->$name;
        }
        return false;
    }

    public function __set($name, $value)
    {
        if (starts_with($name, "_")) {
            $name = "_$name";
        }
        $this->$name = $value;
    }
}
