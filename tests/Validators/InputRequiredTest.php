<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/29/2016
 * Time: 4:19 PM.
 */

namespace WTForms\Tests\Validators;

use PHPUnit\Framework\TestCase;
use WTForms\Exceptions\StopValidation;
use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\InputRequired;

class InputRequiredTest extends TestCase
{
  public $form;

  protected function setUp(): void
  {
    $this->form = new Form();
  }

  public function testInputRequired()
  {
    $this->expectExceptionMessage('This field is required.');
    $this->expectException(StopValidation::class);
    $input_required = new InputRequired();
    $this->assertNull($input_required($this->form, new DummyField(['data' => 'foobar', 'raw_data' => ['foobar']])));
    $this->assertContains('required', $input_required->field_flags);
    $input_required($this->form, new DummyField(['data' => '', 'raw_data' => ['']]));
  }

  public function testCustomMessage()
  {
    $this->expectExceptionMessage('foo');
    $this->expectException(StopValidation::class);
    (new InputRequired('foo'))->__invoke($this->form, new DummyField(['data' => '', 'raw_data' => ['']]));
  }
}
