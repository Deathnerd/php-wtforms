<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/29/2016
 * Time: 3:38 PM
 */

namespace WTForms\Tests\Validators;


use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\DataRequired;
use WTForms\Validators\StopValidation;

class DataRequiredTest extends \PHPUnit_Framework_TestCase
{

  public $form;
  public $field;

  protected function setUp()
  {
    $this->form = new Form();
    $this->field = new DummyField(["data" => "foobar"]);
  }

  public function testDataRequired()
  {
    $data_required = new DataRequired;
    $this->assertNull($data_required($this->form, $this->field));
    $this->assertContains("required", $data_required->field_flags);
  }

  /**
   * @expectedException \WTForms\Validators\StopValidation
   */
  public function testEmptyString()
  {
    $this->field->data = "";
    (new DataRequired)->__invoke($this->form, $this->field);
  }

  /**
   * @expectedException \WTForms\Validators\StopValidation
   * @expectedExceptionMessage This field is required.
   */
  public function testEmptyWhitespaceString()
  {
    $this->field->data = "  ";
    (new DataRequired)->__invoke($this->form, $this->field);
  }

  public function testErrorClobbering()
  {
    $this->field = new DummyField(["data" => ""]);
    $this->field->errors = ["Invalid Integer Value"];

    $this->assertEquals(1, count($this->field->errors));
    $error_caught = false;
    try {
      (new DataRequired)->__invoke($this->form, $this->field);
    } catch (StopValidation $e) {
      $this->assertEquals(0, count($this->field->errors));
      $error_caught = true;
    }
    $this->assertTrue($error_caught, "DataRequired did not throw a StopValidation error in DataRequiredTest::testErrorClobbering");
  }

  /**
   * @expectedException \WTForms\Validators\StopValidation
   * @expectedExceptionMessage foo
   */
  public function testCustomErrorMessage()
  {
    $this->field->data = "";
    (new DataRequired("foo"))->__invoke($this->form, $this->field);
  }

}
