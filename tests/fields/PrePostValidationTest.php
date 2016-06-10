<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/30/2016
 * Time: 12:23 PM
 */

namespace fields;


use WTForms\Exceptions\StopValidation;
use WTForms\Exceptions\ValueError;
use WTForms\Fields\Core\StringField;
use WTForms\Form;
use WTForms\Validators\Length;

class PrePostTestField extends StringField
{
    public function preValidate(Form $form)
    {
        if ($this->data == "stoponly") {
            throw new StopValidation;
        } elseif (starts_with($this->data, "stop")) {
            throw new StopValidation("stop with message");
        } elseif (!is_string($this->data)) {
            throw new ValueError("$this->data is not a string!");
        }
    }

    public function postValidate(Form $form, $stop_validation)
    {
        if ($this->data == "p") {
            throw new ValueError("Post");
        } elseif ($stop_validation && $this->data == "stop-post") {
            throw new ValueError("Post-stopped");
        }
    }
}

class PrePostValidationTest extends \PHPUnit_Framework_TestCase
{

    public function testPreStop()
    {
        $a = $this->initField("long");
        $this->assertEquals(["too long"], $a->errors);

        $a = $this->initField("stoponly");
        $this->assertEquals([], $a->errors);

        $a = $this->initField("stopmessage");
        $this->assertEquals(["stop with message"], $a->errors);

        $a = $this->initField(42);
        $this->assertContains("42 is not a string!", $a->errors);
    }

    public function testPost()
    {
        $a = $this->initField("p");
        $this->assertEquals(["Post"], $a->errors);

        $stopped = $this->initField("stop-post");
        $this->assertEquals(["stop with message", "Post-stopped"], $stopped->errors);
    }

    private function initField($value)
    {
        $form = new Form();
        $form->a = new PrePostTestField(["name" => "a", "validators" => [new Length("too long", ["max" => 1])]]);
        $form->process(["a" => $value]);
        $form->validate();

        return $form->a;
    }

}
