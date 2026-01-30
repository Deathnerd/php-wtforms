<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/29/2016
 * Time: 1:50 PM.
 */

namespace WTForms\Tests\Validators;

use PHPUnit\Framework\TestCase;
use WTForms\Exceptions\ValidationError;
use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\UUID;

class UUIDTest extends TestCase
{
  public $form;
  public $uuid;

  protected function setUp(): void
  {
    $this->form = new Form();
    $this->uuid = new UUID();
  }

  public function testValidData()
  {
    $this->assertSame(
      '2bc1c94f-0deb-43e9-92a1-4775189ec9f8',
      $this->uuid->__invoke($this->form, new DummyField(['data' => '2bc1c94f-0deb-43e9-92a1-4775189ec9f8']))
    );
  }

  public function testInvalid1()
  {
    $this->expectException(ValidationError::class);
    $this->uuid->__invoke($this->form, new DummyField(['data' => '2bc1c94f-deb-43e9-92a1-4775189ec9f8']));
  }

  public function testInvalid2()
  {
    $this->expectException(ValidationError::class);
    $this->uuid->__invoke($this->form, new DummyField(['data' => '2bc1c94f-0deb-43e9-92a1-4775189ec9f']));
  }

  public function testInvalid3()
  {
    $this->expectException(ValidationError::class);
    $this->uuid->__invoke($this->form, new DummyField(['data' => 'gbc1c94f-0deb-43e9-92a1-4775189ec9f8']));
  }

  public function testInvalid4()
  {
    $this->expectException(ValidationError::class);
    $this->uuid->__invoke($this->form, new DummyField(['data' => '2bc1c94f 0deb-43e9-92a1-4775189ec9f8']));
  }
}
