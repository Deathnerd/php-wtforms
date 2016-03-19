<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/18/2016
 * Time: 9:47 PM
 */

namespace WTForms\Tests\Common;


class HelpersTest extends \PHPUnit_Framework_TestCase
{
  public function testHtmlParams()
  {
    $actual = html_params(['foo'        => true,
                           'bar'        => "baz",
                           "data_value" => "shazbot",
                           "id"         => "some_id",
                           "class__"    => ["fa", "fa-envelope"],
                           "for"        => "another_id"]);
    $expected = 'foo bar="baz" data-value="shazbot" id="some_id" class="fa fa-envelope" for="another_id"';
    $this->assertEquals($expected, $actual);
  }

  public function testStartsWith()
  {
    $this->assertTrue(starts_with("_foobar", "_foo"));
    $this->assertFalse(starts_with("_foobar", "_baz"));
    $this->assertFalse(starts_with("_foobar", "bar"));
    $this->assertFalse(starts_with("blah", null));
  }

  public function testStrContains()
  {
    $this->assertTrue(str_contains("_foobar", "oba"));
    $this->assertFalse(str_contains("_foobar", "periwinkle"));
    $this->assertFalse(str_contains("_foobar", null));
  }
}
