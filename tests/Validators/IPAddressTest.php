<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/24/2016
 * Time: 10:41 PM.
 */

namespace WTForms\Tests\Validators;

use PHPUnit\Framework\TestCase;
use WTForms\Exceptions\ValidationError;
use WTForms\Exceptions\ValueError;
use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\IPAddress;

class IPAddressTest extends TestCase
{
  public $form;

  protected function setUp(): void
  {
    $this->form = new Form();
  }

  public function testValidInputs()
  {
    $ip_address = new IPAddress();
    $this->assertNull($ip_address($this->form, new DummyField(['data' => '127.0.0.1'])));
    $ip_address = new IPAddress('', ['ip_type' => FILTER_FLAG_IPV6]);
    foreach (['::1', 'dead:beef:0:0:0:0:42:1', 'abcd:ef::42:1'] as $ipv6_addr) {
      $this->assertNull($ip_address($this->form, new DummyField(['data' => $ipv6_addr])));
    }
  }

  public function testInvalidIPType()
  {
    $this->expectException(ValueError::class);
    $ip_address = new IPAddress('', ['ip_type' => 123]);
  }

  public function testInvalidIPV41()
  {
    $this->expectException(ValidationError::class);
    (new IPAddress())->__invoke($this->form, new DummyField(['data' => 'abc.0.0.1']));
  }

  public function testInvalidIPV42()
  {
    $this->expectException(ValidationError::class);
    (new IPAddress())->__invoke($this->form, new DummyField(['data' => '1278.0.0.1']));
  }

  public function testInvalidIPV43()
  {
    $this->expectException(ValidationError::class);
    (new IPAddress())->__invoke($this->form, new DummyField(['data' => '127.0.0.abc']));
  }

  public function testInvalidIPV44()
  {
    $this->expectException(ValidationError::class);
    (new IPAddress())->__invoke($this->form, new DummyField(['data' => '900.200.100.75']));
  }

  public function testInvalidIPV61()
  {
    $this->expectException(ValidationError::class);
    (new IPAddress('', ['ip_type' => FILTER_FLAG_IPV6]))->__invoke($this->form,
      new DummyField(['data' => 'abc.0.0.1']));
  }

  public function testInvalidIPV62()
  {
    $this->expectException(ValidationError::class);
    (new IPAddress('', ['ip_type' => FILTER_FLAG_IPV6]))->__invoke($this->form,
      new DummyField(['data' => 'abcd:1234::123::1']));
  }

  public function testInvalidIPV63()
  {
    $this->expectException(ValidationError::class);
    (new IPAddress('', ['ip_type' => FILTER_FLAG_IPV6]))->__invoke($this->form,
      new DummyField(['data' => '1:2:3:4:5:6:7:8:9']));
  }

  public function testInvalidIPV64()
  {
    $this->expectException(ValidationError::class);
    (new IPAddress('', ['ip_type' => FILTER_FLAG_IPV6]))->__invoke($this->form,
      new DummyField(['data' => 'abcd::1ffff']));
  }
}
