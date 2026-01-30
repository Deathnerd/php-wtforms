<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/21/2016
 * Time: 10:55 AM.
 */

namespace WTForms\Fields\Core;

/**
 * Class FieldIterator.
 *
 * @property array $entries
 * @template TData
 * @codeCoverageIgnore
 */
trait FieldIterator
{
  /**
   * Return the current element.
   *
   * @see  http://php.net/manual/en/iterator.current.php
   *
   * @return Field<TData>|false can return any type
   *
   * @since 5.0.0
   */
  public function current(): Field|false
  {
    return current($this->entries);
  }

  /**
   * Move forward to the next element.
   *
   * @see  http://php.net/manual/en/iterator.next.php
   *
   * @return void any returned value is ignored
   *
   * @since 5.0.0
   */
  public function next(): void
  {
    next($this->entries);
  }

  /**
   * Return the key of the current element.
   *
   * @see  http://php.net/manual/en/iterator.key.php
   *
   * @return int|string|null scalar on success, or null on failure
   *
   * @since 5.0.0
   */
  public function key(): int|string|null
  {
    return key($this->entries);
  }

  /**
   * Checks if the current position is valid.
   *
   * @see  http://php.net/manual/en/iterator.valid.php
   *
   * @return bool The return value will be cast to boolean and then evaluated.
   *              Returns true on success or false on failure.
   *
   * @since 5.0.0
   */
  public function valid(): bool
  {
    return null !== key($this->entries);
  }

  /**
   * Rewind the Iterator to the first element.
   *
   * @see  http://php.net/manual/en/iterator.rewind.php
   *
   * @return void any returned value is ignored
   *
   * @since 5.0.0
   */
  public function rewind(): void
  {
    reset($this->entries);
  }
}
