<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 4/8/2016
 * Time: 3:43 PM
 */

namespace WTForms\Tests\Fields;


use WTForms\Fields\Core\StringField;
use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\AnyOf;
use WTForms\Validators\InputRequired;
use WTForms\Widgets\Core\TextInput;

/**
 * @property StringField $a
 */
class GenericFieldTestForm extends Form
{
    /**
     * @inheritdoc
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->a = new StringField([
            "attributes" => ["foo" => "bar"],
            "render_kw"  => ["readonly" => true],
            "class"      => "form-control",
            "validators" => [
                new InputRequired("This input is required, yo"),
                new AnyOf("You've gotta match these, guy %s", ["values" => [1, "foo", DIRECTORY_SEPARATOR]]),
            ],
        ]);
        $this->process($options);
    }
}

class GenericFieldTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    }

    public function testProcessData()
    {
        $form = new GenericFieldTestForm();
        $form->a->processFormData([42]);
        $this->assertEquals(42, $form->a->data);
    }

    /**
     * Test whether meta is overridden from parent form's meta
     */
    public function testMetaAttribute()
    {
        $form = new GenericFieldTestForm();
        $field = $form->a;
        $this->assertEquals($form->a->meta, $field->meta);
    }

    public function testRenderKw()
    {
        $form = new GenericFieldTestForm(["a" => "hello"]);
        $expected = '<input class="form-control" foo="bar" id="a" name="a" readonly type="text" value="hello">';
        $this->assertEquals($expected, $form->a->__invoke());
        $expected = '<input class="form-control" foo="baz" id="a" name="a" readonly type="text" value="hello">';
        $this->assertEquals($expected, $form->a->__invoke(['foo' => 'baz']));
        $expected = '<input class="form-control" foo="baz" id="a" name="a" other="hello" type="text" value="hello">';
        $this->assertEquals($expected, $form->a->__invoke(['foo' => 'baz', 'readonly' => false, 'other' => 'hello']));
    }

    public function testPrefix()
    {
        $field = new DummyField(["prefix" => "blah", "name" => "boo"]);
        $expected = '<input id="blahboo" name="blahboo" type="text" value="">';
        $this->assertEquals($expected, (new TextInput)->__invoke($field));
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Method undefined_method not found on form!
     */
    public function testBadMethodCall()
    {
        $field = new DummyField(["prefix" => "blah", "name" => "boo"]);
        $field->undefined_method();
    }

    public function testLabelMethodCall()
    {
        $field = new DummyField(["prefix" => "blah", "name" => "boo"]);
        $this->assertEquals('<label for="blahboo">Boo</label>', $field->label());
        $this->assertEquals('<label for="baz">Blah</label>', $field->label(["text" => "Blah", "for" => "baz"]));
    }

    public function testPopulateObj()
    {
        $field = new DummyField(["name" => "blah", "data" => "foobar"]);
        $obj = new \stdClass();
        $obj->blah = "baz";
        $this->assertEquals("baz", $obj->blah);
        $field->populateObj($obj, "blah");
        $this->assertEquals("foobar", $obj->blah);
    }

    public function testInvokableDefault()
    {
        $field = new DummyField([
            "default" => function () {
                return "foobar";
            },
            "name"    => "blah",
        ]);
        $field->process([]);
        $this->assertTrue(is_callable($field->default));
        $this->assertEquals("foobar", $field->data);
    }

    public function testGetRawData()
    {
        $field = new DummyField(["name" => "blah"]);
        $field->process(["blah" => ["foobar"]]);
        $this->assertEquals("foobar", $field->value);
    }

    public function testClonedInvokableDefault()
    {
        $field = new DummyField([
            "default" => function () {
                return "foobar";
            },
            "name"    => "blah",
        ]);
        $field->process([]);
        $this->assertTrue(is_callable($field->default));
        $cloned_field = clone $field;
        $this->assertTrue(is_callable($cloned_field->default));
        $this->assertEquals("foobar", $field->data);
        $this->assertEquals("foobar", $field->default->__invoke());
    }
}
