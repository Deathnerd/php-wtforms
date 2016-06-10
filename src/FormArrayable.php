<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/6/2016
 * Time: 4:23 PM
 */

namespace WTForms;

trait FormArrayable
{
    /**
     * @internal
     *
     * @param $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->fields);
    }

    /**
     * @internal
     *
     * @param $offset
     *
     * @return null
     */
    public function offsetGet($offset)
    {
        if (array_key_exists($offset, $this->fields)) {
            return $this->fields[$offset];
        }

        return null;
    }

    /**
     * @internal
     *
     * @param $offset
     * @param $value
     */
    public function offsetSet($offset, $value)
    {
        $this->fields[$offset] = $value;
        $this->$offset = $value;
    }

    /**
     * @internal
     *
     * @param $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->fields[$offset]);
        $this->$offset = null;
    }
}