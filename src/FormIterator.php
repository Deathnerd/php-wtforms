<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/22/2016
 * Time: 3:32 PM
 */

namespace WTForms;

/**
 * Class FormIterator
 * @package WTForms
 */
trait FormIterator
{
    /**
     * @inheritdoc
     * @internal
     */
    public function current()
    {
        return current($this->fields);
    }

    /**
     * @inheritdoc
     * @internal
     */
    public function next()
    {
        next($this->fields);
    }

    /**
     * @inheritdoc
     * @internal
     */
    public function key()
    {
        return key($this->fields);
    }

    /**
     * @inheritdoc
     * @internal
     */
    public function valid()
    {
        $key = key($this->fields);
        $var = ($key !== null && $key !== false);

        return $var;
    }

    /**
     * @inheritdoc
     * @internal
     */
    public function rewind()
    {
        reset($this->fields);
    }
}