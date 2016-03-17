<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/14/2016
 * Time: 8:22 PM
 */

namespace wtforms\tests\supporting_classes;

/**
 * @form
 */
class Person
{
  /**
   * @var string
   */
  public $name;

  /**
   * @var string
   * @stringfield('id'=>'foo','classes'=>['bar', 'baz'])
   */
  public $address;

  /**
   * @var int
   */
  public $age;
}