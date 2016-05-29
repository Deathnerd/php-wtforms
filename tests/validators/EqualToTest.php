<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/24/2016
 * Time: 10:19 PM
 */

namespace WTForms\Tests\Validators;


use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\EqualTo;
use WTForms\Exceptions\ValidationError;

class EqualToTest extends \PHPUnit_Framework_TestCase
{
  public $form;

  protected function setUp()
  {
    $this->form = new Form();
  }

  public function testBasic()
  {
    $this->form->foo = new DummyField(["data" => "test"]);
    $triggered_validation_error = false;
    try {
      (new EqualTo("", ["fieldname" => "foo"]))->__invoke($this->form, $this->form['foo']);
    } catch (ValidationError $e) {
      $triggered_validation_error = $e->getMessage();
    }
    $this->assertFalse($triggered_validation_error);
  }

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage EqualTo requires fieldname!
   */
  public function testNoFieldnameSupplied()
  {
    (new EqualTo("", []));
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   */
  public function testInvalidFieldName()
  {
    $this->form->foo = new DummyField(["data" => "test"]);
    (new EqualTo("", ["fieldname" => "invalid_field_name"]))->__invoke($this->form, new DummyField(["data" => "test"]));
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   */
  public function testAnotherInvalidFieldName()
  {
    $this->form->foo = new DummyField(["data" => "test"]);
    (new EqualTo("", ["fieldname" => "foo"]))->__invoke($this->form, new DummyField(["data" => "different_value"]));
  }
}
