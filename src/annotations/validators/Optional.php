<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/27/2016
 * Time: 7:33 PM
 */

namespace WTForms\Annotations\Validators;

/**
 * Allows empty input and stops the validation chain from continuing.
 *
 * If input is empty, also removes prior errors (such as processing errors)
 * from the field
 * @package WTForms\Annotations\Validators
 * @Annotation
 */
class Optional extends ValidatorBase
{
  /**
   * @var bool
   */
  public $strip_whitespace = true;
}