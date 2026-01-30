<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/24/2016
 * Time: 11:00 PM.
 */

namespace WTForms\Tests\Validators;

use PHPUnit\Framework\TestCase;
use WTForms\Exceptions\ValidationError;
use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\MacAddress;

class MacAddressTest extends TestCase
{
  public $form;

  protected function setUp(): void
  {
    $this->form = new Form();
  }

  public function testValidData()
  {
    $mac_address = new MacAddress();
    $this->assertNull($mac_address($this->form, new DummyField(['data' => '01:23:45:67:ab:CD'])));
  }

  public function testInvalid1()
  {
    $this->expectException(ValidationError::class);
    (new MacAddress())->__invoke($this->form, new DummyField(['data' => '00:00:00:00:00']));
  }

  public function testInvalid2()
  {
    $this->expectException(ValidationError::class);
    (new MacAddress())->__invoke($this->form, new DummyField(['data' => '01:23:45:67:89:']));
  }

  public function testInvalid3()
  {
    $this->expectException(ValidationError::class);
    (new MacAddress())->__invoke($this->form, new DummyField(['data' => '01:23:45:67:89:gh']));
  }

  public function testInvalid4()
  {
    $this->expectException(ValidationError::class);
    (new MacAddress())->__invoke($this->form, new DummyField(['data' => '123:23:45:67:89:00']));
  }
}
