<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/29/2016
 * Time: 5:09 PM
 */

namespace WTForms\Tests\Validators;


use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\Regexp;

class RegexpTest extends \PHPUnit_Framework_TestCase
{
  public $form;

  protected function setUp()
  {
    $this->form = new Form();
  }

  public function testRegexp()
  {
    $this->assertEquals('a', (new Regexp("", ["regex" => "/^a/"]))->__invoke($this->form, new DummyField(["data" => "a"])));
    $this->assertEquals('A', (new Regexp("", ["regex" => "/^a/i"]))->__invoke($this->form, new DummyField(["data" => "ABcd"])));
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   * @expectedExceptionMessage Invalid Input.
   */
  public function testInvalidData()
  {
    (new Regexp("", ["regex" => "/^a/"]))->__invoke($this->form, new DummyField(["data" => "foo"]));
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   * @expectedExceptionMessage Invalid Input.
   */
  public function testNullData()
  {
    (new Regexp("", ["regex" => "/^a/"]))->__invoke($this->form, new DummyField(["data" => null]));
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   * @expectedExceptionMessage foo
   */
  public function testCustomMessage()
  {
    (new Regexp("foo", ["regex" => "/^a/"]))->__invoke($this->form, new DummyField(["data" => "f"]));
  }
}
