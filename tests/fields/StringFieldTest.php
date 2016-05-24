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

class StringFieldTest extends \PHPUnit_Framework_TestCase
{
  public function testConstruct()
  {
    $string_field = new StringField(["name" => "string_field", 'widget' => new TextInput(), "data" => "Foobar"]);
    $string_field->process(["string_field" => "Shazbot!"]);
    $actual = $string_field();
    $this->assertContains('id="string_field"', $actual);
    $this->assertContains('type="text"', $actual);
    $this->assertContains('value="Shazbot!"', $actual);
    $this->assertContains('name="string_field"', $actual);
    $string_field = new StringField(["name"   => "string_field",
                                     "id"     => "foobar",
                                     "meta"   => new FooMeta(),
                                     "class_" => "form-control",
                                     'widget' => new TextInput()]);
    $this->assertAttributeInstanceOf('WTForms\Tests\SupportingClasses\FooMeta', 'meta', $string_field);
    $this->assertAttributeInstanceOf('WTForms\Fields\Core\Label', 'label', $string_field);
    $this->assertEquals("String Field", $string_field->label->text);
    $actual = "$string_field";
    $this->assertContains('id="foobar"', $actual);
    $this->assertContains('type="text"', $actual);
    $this->assertContains('value=""', $actual);
    $this->assertContains('class="form-control"', $actual);
    $this->assertContains('name="string_field"', $actual);
  }
}
