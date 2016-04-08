<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/27/2016
 * Time: 7:19 PM
 */

namespace WTForms\Annotations\Validators;

/**
 * Validates that a number is of a minimum and/or maximum value, inclusive.
 * This will work with any comparable number type, such as floats and
 * decimals, not just integers
 * @package WTForms\Annotations\Validators
 * @Annotation
 */
class NumberRange extends ValidatorBase
{
  /**
   * @var float|int
   */
  public $min;

  /**
   * @var float|int
   */
  public $max;
}