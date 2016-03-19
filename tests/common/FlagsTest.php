<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/18/2016
 * Time: 10:28 PM
 */

namespace WTForms\Tests\Common;


use WTForms\Flags;

class FlagsTest extends \PHPUnit_Framework_TestCase
{
  public function testFlags()
  {
    $flags = new Flags();
    $flags->foo = true;
    $flags->_bar = false;
    $this->assertObjectHasAttribute("_foo", $flags);
    $this->assertObjectNotHasAttribute("foo", $flags);
    $this->assertTrue($flags->_foo);
    $this->assertObjectHasAttribute("_bar", $flags);
    $this->assertObjectNotHasAttribute("bar", $flags);
    $this->assertFalse($flags->_bar);
    $this->assertFalse($flags->foo == $flags->_foo);
  }

}
