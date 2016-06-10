<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/18/2016
 * Time: 9:09 PM
 */

namespace WTForms\Tests\Fields;


use WTForms\Fields\Core\Label;
use WTForms\Fields\Core\StringField;
use WTForms\Form;

class LabelTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaultLabelOptions()
    {
        $label = new Label("foobar", "Foo Bar");
        $this->assertEquals('<label for="foobar">Foo Bar</label>', $label());
    }

    public function testLabelInvokeWithOptions()
    {
        $label = new Label("foobar", "Foo Bar");
        $actual = $label([
            "class"      => ["form", "form-label"],
            "text"       => "Shazbot",
            "baz"        => true,
            "data-color" => "red",
            "id"         => "foobar_label"
        ]);
        $expected = '<label baz class="form form-label" data-color="red" for="foobar" id="foobar_label">Shazbot</label>';
        $this->assertEquals($expected, $actual);
        $this->assertEquals("foobar", $label->for);
    }

    public function testLabelToString()
    {
        $label = new Label("foobar", "Foo Bar");
        $actual = "$label";
        $expected = '<label for="foobar">Foo Bar</label>';
        $this->assertEquals($expected, $actual);
        $this->assertEquals($expected, $label->__toString());
    }

    public function testAutoLabel()
    {
        $form = new Form();
        $form->first_name = new StringField(["name" => "fname", "label" => "First Name"]);
        $form->last_name = new StringField(["name" => "last_name"]);
        $this->assertEquals('<label for="fname">First Name</label>', $form['first_name']->label());
        $this->assertEquals('<label for="last_name">Last Name</label>', $form['last_name']->label());
        $this->assertEquals('<label for="last_name"></label>', $form['last_name']->label(["text" => ""]));
    }
}
