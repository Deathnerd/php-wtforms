<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/30/2016
 * Time: 1:00 PM
 */

namespace WTForms\Tests\Fields;

use WTForms\Exceptions\ValueError;
use WTForms\Fields\Core\Field;
use WTForms\Fields\Core\StringField;
use WTForms\Form;

class ProcessMethodsField extends Field
{
    public function processData($value)
    {
        if ($value == "baddata") {
            throw new ValueError("bad data!");
        }
        parent::processData($value);
    }

    public function processFormData(array $valuelist)
    {
        parent::processFormData($valuelist);
        if ($this->data == "badformdata") {
            throw new ValueError("bad form data!");
        }
    }

}


class ProcessMethodsTest extends \PHPUnit_Framework_TestCase
{
    private function initField($value)
    {
        $form = new Form();
        $form->a = new ProcessMethodsField(["name" => "a"]);
        $form->process($value);
        $form->validate();

        return $form->a;
    }

    public function testProcessDataMethod()
    {
        $a = $this->initField(["a" => "foo"]);
        $this->assertEmpty($a->errors);
        $a = $this->initField(["a" => "baddata"]);
        $this->assertEquals(["bad data!"], $a->errors);
    }

    public function testProcessFormDataMethod()
    {
        $a = $this->initField(["formdata" => ["a" => ["foo"]]]);
        $this->assertEmpty($a->errors);
        $a = $this->initField(["formdata" => ["a" => ["badformdata"]]]);
        $this->assertEquals(["bad form data!"], $a->errors);
    }
}
