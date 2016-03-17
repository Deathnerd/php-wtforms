<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/17/2016
 * Time: 4:04 PM
 */

namespace Deathnerd\WTForms\Validators\Annotations;
use mindplay\annotations\Annotation;

/**
 * @usage('property'=>true, 'multiple'=>true,'inherited'=>true))
 */
abstract class ValidatorAnnotationBase extends Annotation
{
  /**
   * @var string The error message to display when this validation fails
   */
  public $message;
}