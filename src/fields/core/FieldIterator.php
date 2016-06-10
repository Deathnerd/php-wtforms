<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/21/2016
 * Time: 10:55 AM
 */

namespace WTForms\Fields\Core;

/**
 * Class FieldIterator
 * @package WTForms\Fields\Core
 * @property array $entries
 * @codeCoverageIgnore
 */
trait FieldIterator
{
    /**
     * Return the current element
     * @link  http://php.net/manual/en/iterator.current.php
     * @return Field Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return current($this->entries);
    }

    /**
     * Move forward to next element
     * @link  http://php.net/manual/en/iterator.next.php
     * @return Field Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        return next($this->entries);
    }

    /**
     * Return the key of the current element
     * @link  http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->entries);
    }

    /**
     * Checks if current position is valid
     * @link  http://php.net/manual/en/iterator.valid.php
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
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->entries);
    }
}
