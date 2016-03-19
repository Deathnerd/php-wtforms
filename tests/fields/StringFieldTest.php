<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/18/2016
 * Time: 10:39 PM
 */

namespace WTForms\Tests\Common;


use WTForms\Fields\Core\StringField;
use WTForms\Form;
use WTForms\Widgets\Core\TextInput;

class StringFieldTest extends \PHPUnit_Framework_TestCase
{
  public function testConstruct()
  {
    $string_field = new StringField("Foobar", ["name" => "string_field"]);
    $string_field->finalize(new Form(), new TextInput());
    $string_field->processData("Shazbot!");
    $this->assertEquals('<input id="string_field" type="text" value="Shazbot!" name="string_field">', $string_field());
    $string_field = new StringField("", ["name"   => "string_field",
                                         "id"     => "foobar",
                                         "meta"   => 'WTForms\Tests\SupportingClasses\FooMeta',
                                         "class_" => "form-control"]);
    $string_field->finalize(new Form(), new TextInput());
    $this->assertAttributeInstanceOf('WTForms\Tests\SupportingClasses\FooMeta', 'meta', $string_field);
    $this->assertAttributeInstanceOf('WTForms\Fields\Core\Label', 'label', $string_field);
    $this->assertEquals("String Field", $string_field->label->text);
    $this->assertEquals('<input id="foobar" type="text" value="" class="form-control" name="string_field">', $string_field->__toString());
  }
}
