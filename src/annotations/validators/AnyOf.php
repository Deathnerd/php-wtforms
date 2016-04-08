<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/25/2016
 * Time: 10:14 PM
 */

namespace WTForms\Annotations\Validators;

/**
 * @Annotation
 * @package WTForms\Annotations\Validators
 */
class AnyOf extends ValidatorBase
{
  /**
   * @var array The values to check against
   */
  public $values = [];
}