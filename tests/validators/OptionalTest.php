<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/29/2016
 * Time: 4:51 PM
 */

namespace WTForms\Tests\Validators;


use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\Optional;
use WTForms\Validators\StopValidation;

class OptionalTest extends \PHPUnit_Framework_TestCase
{
  public $form;

  protected function setUp()
  {
    $this->form = new Form();
  }

  /**
   * @expectedException \WTForms\Validators\StopValidation
   */
  public function testOptional()
  {
    $optional = new Optional();
    $this->assertNull($optional($this->form, new DummyField(["data" => "foobar", "raw_data" => ["foobar"]])));
    $this->assertContains("optional", $optional->field_flags);
    $optional($this->form, new DummyField(["data" => "", "raw_data" => [""]]));
  }

  public function testErrorClobbering()
  {
    $field = new DummyField(["data" => "", 'raw_data' => [""]]);
    $field->errors = ["Invalid Integer Value"];

    $this->assertEquals(1, count($field->errors));
    $error_caught = false;
    try {
      (new Optional)->__invoke($this->form, $field);
    } catch (StopValidation $e) {
      $this->assertEquals(0, count($field->errors));
      $error_caught = true;
    }
    $this->assertTrue($error_caught, "Optional did not throw a StopValidation error in OptionalTest::testErrorClobbering");
  }

  /**
   * @expectedException \WTForms\Validators\StopValidation
   */
  public function testWhitespaceStripping()
  {
    $field = new DummyField(["data" => "  ", "raw_data" => ["  "]]);
    $optional = new Optional("", ["strip_whitespace" => false]);
    $this->assertNull($optional($this->form, $field));
    (new Optional)->__invoke($this->form, $field);
  }
}
