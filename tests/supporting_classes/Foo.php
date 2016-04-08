<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/19/2016
 * Time: 3:16 PM
 */

namespace WTForms\Tests\SupportingClasses;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Foo
 * @Annotation
 * @Target({"CLASS"})
 */
class Foo
{
  /**
   * @var string The parent's name
   */
  public $parent_name = "Foo";
}