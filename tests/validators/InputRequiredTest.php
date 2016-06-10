<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/29/2016
 * Time: 4:19 PM
 */

namespace WTForms\Tests\Validators;


use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\InputRequired;

class InputRequiredTest extends \PHPUnit_Framework_TestCase
{
    public $form;

    protected function setUp()
    {
        $this->form = new Form();
    }

    /**
     * @expectedException \WTForms\Exceptions\StopValidation
     * @expectedExceptionMessage This field is required.
     */
    public function testInputRequired()
    {
        $input_required = new InputRequired;
        $this->assertNull($input_required($this->form, new DummyField(["data" => "foobar", "raw_data" => ["foobar"]])));
        $this->assertContains("required", $input_required->field_flags);
        $input_required($this->form, new DummyField(["data" => "", "raw_data" => [""]]));
    }

    /**
     * @expectedException \WTForms\Exceptions\StopValidation
     * @expectedExceptionMessage foo
     */
    public function testCustomMessage()
    {
        (new InputRequired("foo"))->__invoke($this->form, new DummyField(["data" => "", "raw_data" => [""]]));
    }
}
