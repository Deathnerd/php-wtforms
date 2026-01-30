<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/24/2016
 * Time: 10:19 PM.
 */

namespace WTForms\Tests\Validators;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use WTForms\Exceptions\ValidationError;
use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\EqualTo;

class EqualToTest extends TestCase
{
  public $form;

  protected function setUp(): void
  {
    $this->form = new Form();
  }

  public function testBasic()
  {
    $this->form->foo = new DummyField(['data' => 'test']);
    $triggered_validation_error = false;
    try {
      (new EqualTo('', ['fieldname' => 'foo']))->__invoke($this->form, $this->form['foo']);
    } catch (ValidationError $e) {
      $triggered_validation_error = $e->getMessage();
    }
    $this->assertFalse($triggered_validation_error);
  }

  public function testNoFieldnameSupplied()
  {
    $this->expectExceptionMessage('EqualTo requires fieldname!');
    $this->expectException(RuntimeException::class);
    new EqualTo('', []);
  }

  public function testInvalidFieldName()
  {
    $this->expectException(ValidationError::class);
    $this->form->foo = new DummyField(['data' => 'test']);
    (new EqualTo('', ['fieldname' => 'invalid_field_name']))->__invoke($this->form,
      new DummyField(['data' => 'test']));
  }

  public function testAnotherInvalidFieldName()
  {
    $this->expectException(ValidationError::class);
    $this->form->foo = new DummyField(['data' => 'test']);
    (new EqualTo('', ['fieldname' => 'foo']))->__invoke($this->form, new DummyField(['data' => 'different_value']));
  }
}
