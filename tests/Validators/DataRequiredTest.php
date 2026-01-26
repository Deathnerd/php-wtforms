<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/29/2016
 * Time: 3:38 PM.
 */

namespace WTForms\Tests\Validators;

use WTForms\Exceptions\StopValidation;
use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\DataRequired;

class DataRequiredTest extends \PHPUnit\Framework\TestCase
{
  public $form;
  public $field;

  protected function setUp(): void
  {
    $this->form = new Form();
    $this->field = new DummyField(['data' => 'foobar']);
  }

  public function testDataRequired()
  {
    $data_required = new DataRequired();
    $this->assertNull($data_required($this->form, $this->field));
    $this->assertContains('required', $data_required->field_flags);
  }

  public function testEmptyString()
  {
    $this->expectException(StopValidation::class);
    $this->field->data = '';
    (new DataRequired())->__invoke($this->form, $this->field);
  }

  public function testEmptyWhitespaceString()
  {
    $this->expectExceptionMessage('This field is required.');
    $this->expectException(StopValidation::class);
    $this->field->data = '  ';
    (new DataRequired())->__invoke($this->form, $this->field);
  }

  public function testErrorClobbering()
  {
    $this->field = new DummyField(['data' => '']);
    $this->field->errors = ['Invalid Integer Value'];

    $this->assertEquals(1, count($this->field->errors));
    $error_caught = false;
    try {
      (new DataRequired())->__invoke($this->form, $this->field);
    } catch (StopValidation $e) {
      $this->assertEquals(0, count($this->field->errors));
      $error_caught = true;
    }
    $this->assertTrue($error_caught,
      'DataRequired did not throw a StopValidation error in DataRequiredTest::testErrorClobbering');
  }

  public function testCustomErrorMessage()
  {
    $this->expectExceptionMessage('foo');
    $this->expectException(StopValidation::class);
    $this->field->data = '';
    (new DataRequired('foo'))->__invoke($this->form, $this->field);
  }
}
