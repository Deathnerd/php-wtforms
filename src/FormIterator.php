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
   * Return the current element
   * @link  http://php.net/manual/en/iterator.current.php
   * @return mixed Can return any type.
   * @since 5.0.0
   */
  public function current()
  {
    return current($this->fields);
  }

  /**
   * Move forward to next element
   * @link  http://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   * @since 5.0.0
   */
  public function next()
  {
    next($this->fields);
  }

  /**
   * Return the key of the current element
   * @link  http://php.net/manual/en/iterator.key.php
   * @return mixed scalar on success, or null on failure.
   * @since 5.0.0
   */
  public function key()
  {
    return key($this->fields);
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
    $key = key($this->fields);
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
    reset($this->fields);
  }
}