<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/24/2016
 * Time: 11:00 PM
 */

namespace WTForms\Tests\Validators;


use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\MacAddress;

class MacAddressTest extends \PHPUnit\Framework\TestCase
{
  public $form;

  protected function setUp(): void
  {
    $this->form = new Form();
  }

  public function testValidData()
  {
    $mac_address = new MacAddress;
    $this->assertNull($mac_address($this->form, new DummyField(["data" => "01:23:45:67:ab:CD"])));
  }

  public function testInvalid_1()
  {
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new MacAddress)->__invoke($this->form, new DummyField(["data" => "00:00:00:00:00"]));
  }

  public function testInvalid_2()
  {
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new MacAddress)->__invoke($this->form, new DummyField(["data" => "01:23:45:67:89:"]));
  }

  public function testInvalid_3()
  {
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new MacAddress)->__invoke($this->form, new DummyField(["data" => "01:23:45:67:89:gh"]));
  }

  public function testInvalid_4()
  {
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new MacAddress)->__invoke($this->form, new DummyField(["data" => "123:23:45:67:89:00"]));
  }
}
