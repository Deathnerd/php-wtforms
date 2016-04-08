<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 10:09 PM
 */

namespace WTForms\Annotations\Validators;

/**
 * Validates the field against a user-provided regular expression
 * @package WTForms\Annotations\Validators
 * @Annotation
 */
class Regexp extends ValidatorBase
{
  /**
   * @var string
   */
  public $regex;
}