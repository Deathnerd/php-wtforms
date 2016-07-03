<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 7/3/2016
 * Time: 1:12 PM
 */

namespace WTForms\Tests\Quirks;

use WTForms\Fields\Core\FieldList;
use WTForms\Fields\Core\FormField;
use WTForms\Fields\Core\StringField;
use WTForms\Form;
use WTForms\Validators\AnyOf;
use WTForms\Validators\InputRequired;

class FormFieldInFieldListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ParentForm
     */
    public $form;

    protected function setUp()
    {
        $this->form = ParentForm::create([]);
    }

    public function testIndexing()
    {
        $stuff = "{$this->form->foo}";
        $this->assertEquals($this->form->foo->last_index, $this->form->foo->min_entries - 1);
        $subfields = $this->getSubFields();
        $this->assertCount(5, $subfields);
        $this->assertContainsOnlyInstancesOf('\WTForms\Fields\Core\FormField', $subfields);
    }

    public function testSubfieldNaming()
    {
        $stuff = "{$this->form->foo}";
        $subfields = $this->getSubFields();
        // Test for strictly increasing indexes in names
        for ($i = 0; $i < count($subfields); $i++) {
            $expected = "foo-$i";
            $actual = $subfields[$i]->name;
            $this->assertEquals($expected, $actual, "Expected $expected for subfield $i, got $actual");
        }
    }

    public function testSubfieldLabelling()
    {
        $stuff = "{$this->form->foo}";
        $this->assertEquals('<ul id="foo"><li><label for="foo-0">Foo-0</label> <table id="foo-0"><tr><th><label for="foo-0-bar">Bar</label></th><td><input id="foo-0-bar" name="foo-0-bar" type="text" value="Bar"></td></tr><tr><th><label for="foo-0-baz">Baz</label></th><td><input id="foo-0-baz" name="foo-0-baz" type="text" value="Baz"></td></tr></table></li><li><label for="foo-1">Foo-1</label> <table id="foo-1"><tr><th><label for="foo-1-bar">Bar</label></th><td><input id="foo-1-bar" name="foo-1-bar" type="text" value="Bar"></td></tr><tr><th><label for="foo-1-baz">Baz</label></th><td><input id="foo-1-baz" name="foo-1-baz" type="text" value="Baz"></td></tr></table></li><li><label for="foo-2">Foo-2</label> <table id="foo-2"><tr><th><label for="foo-2-bar">Bar</label></th><td><input id="foo-2-bar" name="foo-2-bar" type="text" value="Bar"></td></tr><tr><th><label for="foo-2-baz">Baz</label></th><td><input id="foo-2-baz" name="foo-2-baz" type="text" value="Baz"></td></tr></table></li><li><label for="foo-3">Foo-3</label> <table id="foo-3"><tr><th><label for="foo-3-bar">Bar</label></th><td><input id="foo-3-bar" name="foo-3-bar" type="text" value="Bar"></td></tr><tr><th><label for="foo-3-baz">Baz</label></th><td><input id="foo-3-baz" name="foo-3-baz" type="text" value="Baz"></td></tr></table></li><li><label for="foo-4">Foo-4</label> <table id="foo-4"><tr><th><label for="foo-4-bar">Bar</label></th><td><input id="foo-4-bar" name="foo-4-bar" type="text" value="Bar"></td></tr><tr><th><label for="foo-4-baz">Baz</label></th><td><input id="foo-4-baz" name="foo-4-baz" type="text" value="Baz"></td></tr></table></li></ul>', $stuff);
    }

    public function testValidateWithNoData()
    {
        // First test with no data
        $this->assertFalse($this->form->validate());
        $errors = $this->form->errors;
        $this->assertCount(1, $errors);
        $this->assertCount(5, $errors['foo']);
        $expected_errors = [];
        for ($i = 0; $i < 5; $i++) {
            $expected_errors[] = ["bar" => ["Bar needs a value!",]];
        }
        $this->assertEquals($expected_errors, $errors['foo']);
    }

    public function testValidateWithInvalidData()
    {
        $form = ParentForm::create(["formdata" => [
            "foo-0-bar" => "blah",
            "foo-1-bar" => "boo",
            "foo-2-bar" => "foobar",
            "foo-3-bar" => "blah",
            "foo-4-bar" => "boo",
        ]]);
        $this->assertFalse($form->validate());
        $this->assertCount(1, $form->errors['foo']);
        $this->assertEquals('Invalid value, must be one of: blah, boo.', $form->errors['foo'][0]['bar'][0]);
    }

    public function testValidateWithValidData()
    {
        $form = ParentForm::create(["formdata" => [
            "foo-0-bar" => "blah",
            "foo-1-bar" => "boo",
            "foo-2-bar" => "blah",
            "foo-3-bar" => "blah",
            "foo-4-bar" => "boo",
        ]]);
        $this->assertTrue($form->validate());
        $this->assertEmpty($form->errors);
    }

    public function testPopulatedFromFormdata()
    {
        $form = ParentForm::create(["formdata" => [
            "foo-0-bar" => "blah",
            "foo-1-bar" => "boo",
            "foo-2-bar" => "blah",
            "foo-3-bar" => "blah",
            "foo-4-bar" => "boo",
        ]]);
        $this->assertEquals('blah', $form->foo[0]['bar']->data);
        $this->assertEquals('boo', $form->foo[1]['bar']->data);
        $this->assertEquals('blah', $form->foo[2]['bar']->data);
        $this->assertEquals('blah', $form->foo[3]['bar']->data);
        $this->assertEquals('boo', $form->foo[4]['bar']->data);
    }

    public function testPopulatedFromDataArray()
    {
        $form = ParentForm::create(["data" => [
            "foo" => [["bar" => "blah"],
                ["bar" => "boo"],
                ["bar" => "blah"],
                ["bar" => "blah"],
                ["bar" => "boo"],],
        ]]);
        $this->assertEquals('blah', $form->foo[0]['bar']->data);
        $this->assertEquals('boo', $form->foo[1]['bar']->data);
        $this->assertEquals('blah', $form->foo[2]['bar']->data);
        $this->assertEquals('blah', $form->foo[3]['bar']->data);
        $this->assertEquals('boo', $form->foo[4]['bar']->data);
    }

    public function testPopulatedFromObject()
    {
        $obj = (object)[
            "foo" => [["bar" => "blah"],
                ["bar" => "boo"],
                ["bar" => "blah"],
                ["bar" => "blah"],
                ["bar" => "boo"]],
        ];
        $form = ParentForm::create(["obj" => $obj]);
        $this->assertEquals('blah', $form->foo[0]['bar']->data);
        $this->assertEquals('boo', $form->foo[1]['bar']->data);
        $this->assertEquals('blah', $form->foo[2]['bar']->data);
        $this->assertEquals('blah', $form->foo[3]['bar']->data);
        $this->assertEquals('boo', $form->foo[4]['bar']->data);
    }


    /**
     * @return array<FormField>
     */
    private function getSubFields()
    {
        $subfields = [];
        foreach ($this->form->foo as $subfield) {
            $subfields[] = $subfield;
        }

        return $subfields;
    }
}

/**
 * @property FieldList foo
 */
class ParentForm extends Form
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->foo = new FieldList(["inner_field" => new FormField(["form_class" => new ChildForm]),
                                    "min_entries" => 5]);
    }
}

/**
 * @property StringField bar
 * @property StringField baz
 */
class ChildForm extends Form
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->bar = new StringField(["default"    => "Bar",
                                      "validators" =>
                                          [
                                              new InputRequired("Bar needs a value!"),
                                              new AnyOf("", ["values" => ["blah", "boo"]]),
                                          ],
        ]);
        $this->baz = new StringField(["default" => "Baz"]);
    }
}
