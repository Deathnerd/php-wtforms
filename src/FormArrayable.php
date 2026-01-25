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
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->fields);
    }

    /**
     * @internal
     *
     * @param mixed $offset
     *
     * @return \WTForms\Fields\Core\Field|null
     */
    public function offsetGet($offset): ?Fields\Core\Field
    {
        if (array_key_exists($offset, $this->fields)) {
            return $this->fields[$offset];
        }

        return null;
    }

    /**
     * @internal
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->fields[$offset] = $value;
        $this->$offset = $value;
    }

    /**
     * @internal
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->fields[$offset]);
        $this->$offset = null;
    }
}