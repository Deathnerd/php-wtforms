<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/17/2016
 * Time: 4:11 PM
 */

namespace Deathnerd\WTForms\Validators\Annotations;

/**
 * @usage('property'=>true,'multiple'=>false,'inherited'=>true)
 */
class InputRequiredAnnotation extends ValidatorAnnotationBase
{
  /**
   * @var string
   */
  public $message = "";
}