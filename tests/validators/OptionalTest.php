<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/29/2016
 * Time: 4:51 PM
 */

namespace WTForms\Tests\Validators;


use WTForms\Exceptions\StopValidation;
use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\Optional;

class OptionalTest extends \PHPUnit\Framework\TestCase
{
  public $form;

  protected function setUp(): void
  {
    $this->form = new Form();
  }

  public function testOptional()
  {
    $this->expectException(\WTForms\Exceptions\StopValidation::class);
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
    $this->assertTrue($error_caught,
      "Optional did not throw a StopValidation error in OptionalTest::testErrorClobbering");
  }

  public function testWhitespaceStripping()
  {
    $this->expectException(\WTForms\Exceptions\StopValidation::class);
    $field = new DummyField(["data" => "  ", "raw_data" => ["  "]]);
    $optional = new Optional("", ["strip_whitespace" => false]);
    $this->assertNull($optional($this->form, $field));
    (new Optional)->__invoke($this->form, $field);
  }
}
