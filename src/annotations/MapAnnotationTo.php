<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/19/2016
 * Time: 2:57 PM
 */

namespace WTForms\Annotations;

/**
 * Class MapTo
 * @package Deathnerd\WTForms
 * @Annotation
 * @Target({"ANNOTATION", "CLASS"})
 */
class MapAnnotationTo
{
  /**
   * @var string The class path to map this annotation to
   */
  public $class_path;
}