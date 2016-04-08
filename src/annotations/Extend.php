<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/19/2016
 * Time: 3:27 PM
 */

namespace WTForms\Annotations;


use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class Extend
{
  /**
   * @var string The parent annotation to extend
   */
  public $parent_annotation;
}