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

class MacAddressTest extends \PHPUnit_Framework_TestCase
{
    public $form;

    protected function setUp()
    {
        $this->form = new Form();
    }

    public function testValidData()
    {
        $mac_address = new MacAddress;
        $this->assertNull($mac_address($this->form, new DummyField(["data" => "01:23:45:67:ab:CD"])));
    }

    /**
     * @expectedException \WTForms\Exceptions\ValidationError
     */
    public function testInvalid_1()
    {
        (new MacAddress)->__invoke($this->form, new DummyField(["data" => "00:00:00:00:00"]));
    }

    /**
     * @expectedException \WTForms\Exceptions\ValidationError
     */
    public function testInvalid_2()
    {
        (new MacAddress)->__invoke($this->form, new DummyField(["data" => "01:23:45:67:89:"]));
    }

    /**
     * @expectedException \WTForms\Exceptions\ValidationError
     */
    public function testInvalid_3()
    {
        (new MacAddress)->__invoke($this->form, new DummyField(["data" => "01:23:45:67:89:gh"]));
    }

    /**
     * @expectedException \WTForms\Exceptions\ValidationError
     */
    public function testInvalid_4()
    {
        (new MacAddress)->__invoke($this->form, new DummyField(["data" => "123:23:45:67:89:00"]));
    }
}
