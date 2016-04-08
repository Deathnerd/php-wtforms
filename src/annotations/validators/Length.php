<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/22/2016
 * Time: 8:21 PM
 */

namespace WTForms\Annotations\Validators;


use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Validates the length of a string
 * @package WTForms\Annotations\Validators
 * @Annotation
 */
class Length extends ValidatorBase
{
  /**
   * @var int The minimum required length of the string
   */
  public $min;
  /**
   * @var int The maximum length of the string
   */
  public $max;
}
