<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/21/2016
 * Time: 10:58 AM
 */

namespace WTForms;


/**
 * Holds a set of boolean flags as attributes
 *
 * Accessing a non-existing attribute returns false for its value
 * @package WTForms
 */
class Flags
{
    /**
     * @internal
     */
    public function __get($name)
    {
        return property_exists($this, $name) ? $this->$name : $this->$name = false;
    }

    /**
     * @internal
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}
