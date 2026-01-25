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

class RegexpTest extends \PHPUnit\Framework\TestCase
{
  public $form;

  protected function setUp(): void
  {
    $this->form = new Form();
  }

  public function testRegexp()
  {
    $this->assertEquals('a',
      (new Regexp("", ["regex" => "/^a/"]))->__invoke($this->form, new DummyField(["data" => "a"])));
    $this->assertEquals('A',
      (new Regexp("", ["regex" => "/^a/i"]))->__invoke($this->form, new DummyField(["data" => "ABcd"])));
  }

  public function testInvalidData()
  {
    $this->expectExceptionMessage("Invalid Input.");
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new Regexp("", ["regex" => "/^a/"]))->__invoke($this->form, new DummyField(["data" => "foo"]));
  }

  public function testNullData()
  {
    $this->expectExceptionMessage("Invalid Input.");
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new Regexp("", ["regex" => "/^a/"]))->__invoke($this->form, new DummyField(["data" => null]));
  }

  public function testCustomMessage()
  {
    $this->expectExceptionMessage("foo");
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new Regexp("foo", ["regex" => "/^a/"]))->__invoke($this->form, new DummyField(["data" => "f"]));
  }
}
