<?php

namespace WTForms\Internal\Types;

/**
 * @template TKey of string
 * @template TValue
 * @uses HashMapTrait<TKey, TValue>
 */
class GenericHashMap implements HashMapInterface
{
  use HashMapTrait;

  /**
   * @param list<TKey, TValue> $items
   */
  public function __construct(array $items = [])
  {
    $this->initMap($items);
  }

  public function __clone()
  {
    $this->initMap($this->data);
  }

}