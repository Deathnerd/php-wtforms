<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/17/2016
 * Time: 4:11 PM
 */

namespace WTForms\Annotations\Validators;

/**
 * @Annotation
 */
class InputRequired extends ValidatorBase
{
  /**
   * @var string
   */
  public $message = "";
  
  /**
   * @var array
   */
  public $field_flags = ['required'];
}