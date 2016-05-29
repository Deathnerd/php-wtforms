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
use WTForms\Validators\ValidationError;
use \Mockery;

class EqualToTest extends \PHPUnit_Framework_TestCase
{
  public $form;

  /**
   * Tears down the fixture, for example, close a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown()
  {
    Mockery::close();
  }


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
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testInvalidFieldName()
  {
    $this->form->foo = new DummyField(["data" => "test"]);
    (new EqualTo("", ["fieldname" => "invalid_field_name"]))->__invoke($this->form, new DummyField(["data" => "test"]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testAnotherInvalidFieldName()
  {
    $this->form->foo = new DummyField(["data" => "test"]);
    (new EqualTo("", ["fieldname" => "foo"]))->__invoke($this->form, new DummyField(["data" => "different_value"]));
  }
}
