<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/19/2016
 * Time: 3:16 PM
 */

namespace WTForms\Tests\SupportingClasses;
use WTForms\Annotations\Extend;


/**
 * Class Bar
 * @Annotation
 * @Extend(parent_annotation="WTForms\Tests\SupportingClasses\Foo")
 */
class Bar
{
  /**
   * @var string The name of the child
   */
  public $child_name = "Bar";

  public function shazbot($foo){
    $foo += 1;
    echo $foo;
    return $foo;
  }
}