<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/29/2016
 * Time: 4:19 PM.
 */

namespace WTForms\Tests\Validators;

use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\InputRequired;

class InputRequiredTest extends \PHPUnit\Framework\TestCase
{
  public $form;

  protected function setUp(): void
  {
    $this->form = new Form();
  }

  public function testInputRequired()
  {
    $this->expectExceptionMessage('This field is required.');
    $this->expectException(\WTForms\Exceptions\StopValidation::class);
    $input_required = new InputRequired();
    $this->assertNull($input_required($this->form, new DummyField(['data' => 'foobar', 'raw_data' => ['foobar']])));
    $this->assertContains('required', $input_required->field_flags);
    $input_required($this->form, new DummyField(['data' => '', 'raw_data' => ['']]));
  }

  public function testCustomMessage()
  {
    $this->expectExceptionMessage('foo');
    $this->expectException(\WTForms\Exceptions\StopValidation::class);
    (new InputRequired('foo'))->__invoke($this->form, new DummyField(['data' => '', 'raw_data' => ['']]));
  }
}
