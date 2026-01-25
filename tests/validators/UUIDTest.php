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

class UUIDTest extends \PHPUnit\Framework\TestCase
{
    public $form;
    public $uuid;

    protected function setUp(): void
    {
        $this->form = new Form();
        $this->uuid = new UUID;
    }

    public function testValidData()
    {
        $this->assertSame(
            "2bc1c94f-0deb-43e9-92a1-4775189ec9f8",
            $this->uuid->__invoke($this->form, new DummyField(["data" => "2bc1c94f-0deb-43e9-92a1-4775189ec9f8"]))
        );
    }

    public function testInvalid_1()
    {
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        $this->uuid->__invoke($this->form, new DummyField(["data" => "2bc1c94f-deb-43e9-92a1-4775189ec9f8"]));
    }

    public function testInvalid_2()
    {
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        $this->uuid->__invoke($this->form, new DummyField(["data" => "2bc1c94f-0deb-43e9-92a1-4775189ec9f"]));
    }

    public function testInvalid_3()
    {
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        $this->uuid->__invoke($this->form, new DummyField(["data" => "gbc1c94f-0deb-43e9-92a1-4775189ec9f8"]));
    }

    public function testInvalid_4()
    {
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        $this->uuid->__invoke($this->form, new DummyField(["data" => "2bc1c94f 0deb-43e9-92a1-4775189ec9f8"]));
    }

}
