<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 4:30 PM.
 */

namespace WTForms\Fields\HTML5;

use WTForms\Widgets\HTML5\RangeInput;

/**
 * Represents an ``<input type="range">``.
 */
class IntegerRangeField extends IntegerField
{
  /**
   * @param array<string, mixed> $options
   */
  public function __construct(array $options = [])
  {
    $options = array_merge(['widget' => new RangeInput(['step' => '1'])], $options);
    parent::__construct($options);
  }
}
