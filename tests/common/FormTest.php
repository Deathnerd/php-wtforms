<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/15/2016
 * Time: 9:11 PM
 */

namespace WTForms\Tests\Common;

use WTForms\DefaultMeta;
use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Core\Field;
use WTForms\Fields\Core\StringField;
use WTForms\Form;
use WTForms\Validators\DataRequired;

/**
 * @property StringField a
 * @property StringField b
 * @property StringField c
 */
class TestForm extends Form
{
    public $prefix = "Foo";

    public function __construct(array $options = [])
    {
        $this->meta = new DefaultMeta();
        parent::__construct($options);
        $this->a = new StringField([
            "name" => "a",
            "validators" => [new DataRequired()]
        ]);
        $this->b = new StringField(["name" => "b"], $this);
        $this->c = new StringField(["name" => "c"]);
    }

    public function validate_c(Form $form, Field $field)
    {
        if ($field->data == "error") {
            throw new ValidationError("C has data!");
        }
    }
}

class FormTest extends \PHPUnit_Framework_TestCase
{

    public function testFormCreation()
    {
        $form = new TestForm();
        $this->assertEquals("Foo-", $form->prefix);
        $this->assertTrue($form->a instanceof Field);
        $this->assertTrue($form->a->form === $form);
        $this->assertFalse($form->validate());
    }

    public function testFieldUnset()
    {
        $form = new TestForm();
        $this->assertNotNull($form->a);
        $this->assertTrue($form->a instanceof Field && $form->a instanceof StringField);
        unset($form->a);
        $this->assertNull($form->a);
    }

    public function testPostDataPopulate()
    {
        $post_data = ["Foo-a" => ["foo"]];
        $form = new TestForm();
        $form->process(["formdata" => $post_data]);
        $this->assertEquals("foo", $form->a->data);
        $this->assertTrue($form->validate());
        $form->a->data = null;
        $this->assertFalse($form->validate());
    }

    public function testExtraDataPopulate()
    {
        $form = new TestForm();
        $form->process(["a" => "foo"]);
        $this->assertEquals("foo", $form->a->data);
        $this->assertTrue($form->validate());
    }

    public function testFormPropertySettingAndUnsetting()
    {
        $form = new TestForm();
        // Test assigning data to a field
        $form->a = "foo";
        $this->assertTrue($form->a instanceof StringField);
        $this->assertEquals("foo", $form->a->data);
        $form->a->data = "baz";
        $this->assertEquals("baz", $form->a->data);

        // test for non-field attribute assignment
        $form->foo = "bar";
        $this->assertObjectHasAttribute("foo", $form);
        $this->assertEquals("bar", $form->foo);

        // test unsetting dynamic form field assignment
        unset($form->a);
        $this->assertObjectNotHasAttribute("a", $form);
        $this->assertNull($form->a);

        // test unsetting non-field dynamic assignment
        unset($form->foo);
        $this->assertObjectNotHasAttribute("foo", $form);
        $this->assertNull($form->foo);
    }

    public function testOrderedField()
    {
        $form = new TestForm();
        $fields = [];
        foreach ($form as $field) {
            $fields[] = $field->name;
        }
        $this->assertTrue($fields[0] == "Foo-a");
        $this->assertTrue($fields[1] == "Foo-b");
        $this->assertTrue($fields[2] == "Foo-c");
        unset($form->b);
        $fields = [];
        foreach ($form as $field) {
            $fields[] = $field->name;
        }
        $this->assertTrue($fields[0] == "Foo-a");
        $this->assertTrue($fields[1] == "Foo-c");
    }

    public function testFormSerialize()
    {
        $form = new TestForm();
        $serialized = serialize($form);
        $unserialized = unserialize($serialized);
        $this->assertTrue($unserialized instanceof $form);
        $this->assertEquals($form, $unserialized);
    }

    public function testPopulateObj()
    {
        $form = new TestForm();
        $form->process(['a' => "foobar"]);
        $obj = new \stdClass();
        $obj->a = "baz";
        $obj = $form->populateObj($obj);
        $this->assertEquals("foobar", $obj->a);
    }

    public function testPopulateArray()
    {
        $form = new TestForm();
        $form->process(['a' => "foobar"]);
        $arr = ["a" => "baz"];
        $arr = $form->populateArray($arr);
        $this->assertEquals("foobar", $arr['a']);
    }

    public function testPopulate()
    {
        $form = new TestForm();
        $form->process(["a" => "foobar"]);
        $obj = new \stdClass();
        $obj->a = "baz";
        $arr = ["a" => "baz"];
        $this->assertEquals("foobar", $form->populate($obj)->a);
        $this->assertEquals("foobar", $form->populate($arr)["a"]);
    }

    public function testDataArg()
    {
        $form = new TestForm();
        $form->process(["data" => ["a" => "foo"]]);
        $this->assertEquals("foo", $form->a->data);
        $form = new TestForm();
        $form->process(["data" => ["a" => "foo"], "a" => "bar"]);
        $this->assertEquals("bar", $form->a->data);
    }

    public function testUpdateMeta()
    {
        $form = new TestForm(["meta" => ["csrf_secret" => "shhhdonttell"]]);
        $this->assertEquals("shhhdonttell", $form->meta->csrf_secret);
    }

    public function testFormErrors()
    {
        $form = new TestForm();
        $this->assertFalse($form->validate());
        $this->assertNotEmpty($form->errors);
        $this->assertEquals(["a" => ["This field is required."]], $form->errors);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Form::populate accepts only an array or an object as input; string given.
     */
    public function testInvalidPopulateArgument()
    {
        (new TestForm())->populate("foo");
    }

    public function testExtraValidator()
    {
        $form = new TestForm();
        $form->process(["formdata" => ["Foo-c" => ["error"]]]);
        $this->assertFalse($form->validate());
        $this->assertEquals(["C has data!"], $form->errors["c"]);
    }

    public function testAutoLabel()
    {
        $form = new Form();
        $form->first_name = new StringField();
        $form->foo_bar = new StringField([
            "label" => "foo baz"
        ]);
        $this->assertEquals("First Name", $form->first_name->label->text);
        $this->assertEquals("foo baz", $form->foo_bar->label->text);
    }

    public function testCreateMethod()
    {
        $form = new TestForm();
        $form->process(["formdata" => ["Foo-c" => ["error"]]]);

        $form2 = TestForm::create(["formdata" => ["Foo-c" => ["error"]]]);
        $this->assertInstanceOf('\WTForms\Form', $form);
        $this->assertInstanceOf('\WTForms\Form', $form2);
        $this->assertFalse($form->validate());
        $this->assertFalse($form2->validate());
        $this->assertEquals(["C has data!"], $form2->errors["c"]);
        $this->assertEquals($form->errors, $form2->errors);
    }

}
