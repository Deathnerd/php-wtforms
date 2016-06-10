<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/29/2016
 * Time: 1:50 PM
 */

namespace WTForms\Tests\Validators;


use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\UUID;

class UUIDTest extends \PHPUnit_Framework_TestCase
{
    public $form;
    public $uuid;

    protected function setUp()
    {
        $this->form = new Form();
        $this->uuid = new UUID;
    }

    public function testValidData()
    {
        $this->assertNull($this->uuid->__invoke($this->form,
            new DummyField(["data" => "2bc1c94f-0deb-43e9-92a1-4775189ec9f8"])));
    }

    /**
     * @expectedException \WTForms\Exceptions\ValidationError
     */
    public function testInvalid_1()
    {
        $this->uuid->__invoke($this->form, new DummyField(["data" => "2bc1c94f-deb-43e9-92a1-4775189ec9f8"]));
    }

    /**
     * @expectedException \WTForms\Exceptions\ValidationError
     */
    public function testInvalid_2()
    {
        $this->uuid->__invoke($this->form, new DummyField(["data" => "2bc1c94f-0deb-43e9-92a1-4775189ec9f"]));
    }

    /**
     * @expectedException \WTForms\Exceptions\ValidationError
     */
    public function testInvalid_3()
    {
        $this->uuid->__invoke($this->form, new DummyField(["data" => "gbc1c94f-0deb-43e9-92a1-4775189ec9f8"]));
    }

    /**
     * @expectedException \WTForms\Exceptions\ValidationError
     */
    public function testInvalid_4()
    {
        $this->uuid->__invoke($this->form, new DummyField(["data" => "2bc1c94f 0deb-43e9-92a1-4775189ec9f8"]));
    }

}
