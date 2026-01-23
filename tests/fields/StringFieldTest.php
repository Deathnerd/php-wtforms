<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/18/2016
 * Time: 10:39 PM
 */

namespace WTForms\Tests\Common;


use WTForms\Fields\Core\StringField;
use WTForms\Tests\SupportingClasses\FooMeta;
use WTForms\Widgets\Core\TextInput;

class StringFieldTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $string_field = new StringField(["name" => "string_field", 'widget' => new TextInput(), "data" => "Foobar"]);
        $string_field->process(["string_field" => "Shazbot!"]);
        $this->assertEquals('<input data="Foobar" id="string_field" name="string_field" type="text" value="Shazbot!">',
            $string_field());

        $string_field = new StringField([
            "name"   => "string_field",
            "id"     => "foobar",
            "meta"   => new FooMeta(),
            "class_" => "form-control",
            'widget' => new TextInput()
        ]);
        $this->assertInstanceOf('WTForms\Tests\SupportingClasses\FooMeta', $string_field->meta);
        $this->assertInstanceOf('WTForms\Fields\Core\Label', $string_field->label);
        $this->assertEquals("String Field", $string_field->label->text);
        $this->assertEquals('<input class="form-control" id="foobar" name="string_field" type="text" value="">',
            "$string_field");

        $this->assertNull($string_field->undefined_property);
    }
}
