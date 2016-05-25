<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/24/2016
 * Time: 10:41 PM
 */

namespace WTForms\Tests\Validators;


use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\IPAddress;

class IPAddressTest extends \PHPUnit_Framework_TestCase
{
  public $form;

  protected function setUp()
  {
    $this->form = new Form();
  }

  public function testValidInputs()
  {
    $ip_address = new IPAddress();
    $this->assertNull($ip_address($this->form, new DummyField(["data" => "127.0.0.1"])));
    $ip_address = new IPAddress("", ["ip_type" => FILTER_FLAG_IPV6]);
    foreach (["::1", "dead:beef:0:0:0:0:42:1", "abcd:ef::42:1"] as $ipv6_addr) {
      $this->assertNull($ip_address($this->form, new DummyField(["data" => $ipv6_addr])));
    }
  }

  /**
   * @expectedException \WTForms\ValueError
   */
  public function testInvalidIPType()
  {
    $ip_address = new IPAddress("", ["ip_type" => 123]);
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testInvalidIPV4_1()
  {
    (new IPAddress)->__invoke($this->form, new DummyField(["data" => "abc.0.0.1"]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testInvalidIPV4_2()
  {
    (new IPAddress)->__invoke($this->form, new DummyField(["data" => "1278.0.0.1"]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testInvalidIPV4_3()
  {
    (new IPAddress)->__invoke($this->form, new DummyField(["data" => "127.0.0.abc"]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testInvalidIPV4_4()
  {
    (new IPAddress)->__invoke($this->form, new DummyField(["data" => "900.200.100.75"]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testInvalidIPV6_1()
  {
    (new IPAddress("", ["ip_type" => FILTER_FLAG_IPV6]))->__invoke($this->form, new DummyField(["data" => "abc.0.0.1"]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testInvalidIPV6_2()
  {
    (new IPAddress("", ["ip_type" => FILTER_FLAG_IPV6]))->__invoke($this->form, new DummyField(["data" => "abcd:1234::123::1"]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testInvalidIPV6_3()
  {
    (new IPAddress("", ["ip_type" => FILTER_FLAG_IPV6]))->__invoke($this->form, new DummyField(["data" => "1:2:3:4:5:6:7:8:9"]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testInvalidIPV6_4()
  {
    (new IPAddress("", ["ip_type" => FILTER_FLAG_IPV6]))->__invoke($this->form, new DummyField(["data" => "abcd::1ffff"]));
  }
}
