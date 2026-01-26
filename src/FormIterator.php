<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/22/2016
 * Time: 3:32 PM.
 */

namespace WTForms;

/**
 * Class FormIterator.
 */
trait FormIterator
{
  /**
   * @internal
   */
  public function current()
  {
    return current($this->fields);
  }

  /**
   * @internal
   */
  public function next()
  {
    next($this->fields);
  }

  /**
   * @internal
   */
  public function key()
  {
    return key($this->fields);
  }

  /**
   * @internal
   */
  public function valid(): bool
  {
    return null !== key($this->fields);
  }

  /**
   * @internal
   */
  public function rewind()
  {
    reset($this->fields);
  }
}
