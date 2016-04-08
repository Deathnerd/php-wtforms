<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/17/2016
 * Time: 4:04 PM
 */

namespace WTForms\Annotations\Validators;
use Doctrine\Common\Annotations\Annotation;


/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class ValidatorBase
{
  /**
   * @var string The error message to display when this validation fails
   */
  public $message;

  /**
   * @var array
   */
  public $field_flags = [];
}