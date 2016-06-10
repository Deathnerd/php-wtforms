<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/17/2016
 * Time: 3:16 PM
 */

namespace WTForms\Tests\Fields;


use WTForms\Exceptions\ValueError;
use WTForms\Fields\Core\Field;
use WTForms\Fields\Core\FieldList;
use WTForms\Fields\Core\FormField;
use WTForms\Fields\Core\StringField;
use WTForms\Form;
use WTForms\Validators\DataRequired;
use WTForms\Validators\Validator;

class FChild extends Form
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->a = new StringField(["validators" => [new DataRequired()], "name" => "a"]);
        $this->process($options);
    }

}

class FieldListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StringField
     */
    public $t;

    public function setUp()
    {
        $this->t = new StringField(["validators" => [new DataRequired()]]);
    }

    public function testForm()
    {
        $form = new Form();
        $form->a = new FieldList(["inner_field" => $this->t]);
        $data = ['foo', 'hi', 'rawr'];
        $form->process(["a" => $data]);
        $a = $form->a;
        $this->assertEquals("hi", $a->entries[1]->data);
        $this->assertEquals("a-1", $a->entries[1]->name);
        $this->assertEquals(count($data), count(array_intersect($a->data, $data)));
        $this->assertEquals(3, count($a->entries));

        $post_data = [
            "a-0" => ["bleh"],
            "a-3" => ["yarg"],
            "a-4" => [""],
            "a-7" => ["mmm"]
        ];
        $form->process(["formdata" => $post_data]);
        $this->assertEquals(4, count($form->a->entries));
        $this->assertEquals(["bleh", "yarg", "", "mmm"], $form->a->data);
        $this->assertFalse($form->validate());


        $form->process(["formdata" => $post_data, "a" => $data]);
        $this->assertEquals(["bleh", "yarg", "", "mmm"], $form->a->data);
        $this->assertFalse($form->validate());

        // test for formdata precedence
        $post_data = ["a-0" => ["a"], "a-1" => ["b"]];
        $form->process(["formdata" => $post_data, "a" => $data]);
        $this->assertEquals(2, count($form->a->entries));
        $this->assertEquals($form->a->data, ["a", "b"]);
        $expected = [];
        // Testing iteration
        foreach ($form->a as $entry) {
            $expected[] = $entry;
        }
        $this->assertEquals($expected, $form->a->entries);
        // Testing countable
        $this->assertEquals(2, count($form->a));
    }

    /**
     * @expectedException \WTForms\Exceptions\TypeError
     * @expectedExceptionMessage populate_obj: cannot find a value to populate from the provided obj or input
     *                           data/defaults
     */
    public function testEnclosedSubform()
    {
        $form = new Form();
        $form->a = new FieldList([
            "inner_field" => new FormField([
                "form_class" => '\WTForms\Tests\Fields\FChild',
                "default"    => function () {
                    return (object)["a" => null];
                }
            ])
        ]);
        $data = [["a" => "hello"]];
        $form->process(["a" => $data]);
        $this->assertEquals($data, $form->a->data);
        $this->assertTrue($form->validate());
        $form->a->appendEntry();
        $this->assertEquals([$data[0], ["a" => null]], $form->a->data);
        $this->assertFalse($form->validate());

        $pdata = ["a-0" => ["fake"], "a-0-a" => ["foo"], "a-1-a" => ["bar"]];
        $form->process(["a" => $data, "formdata" => $pdata]);
        $this->assertEquals([["a" => "foo"], ["a" => "bar"]], $form->a->data);

        $inner_obj = (object)["a" => null];
        $inner_list = [$inner_obj];
        $obj = (object)["a" => $inner_list];
        $form->populateObj($obj);
        $this->assertTrue($obj->a !== $inner_list);
        $this->assertEquals(2, count($obj->a));
        $this->assertTrue($obj->a[0] === $inner_obj);
        $this->assertEquals('foo', $obj->a[0]->a);
        $this->assertEquals('bar', $obj->a[1]->a);

        // Test failure on populate
        $obj2 = (object)["a" => 42];
        $form->populateObj($obj2);
    }


    /**
     * @expectedException \WTForms\Exceptions\IndexError
     */
    public function testEntryManagement()
    {
        $form = new Form();
        $form->a = new FieldList(["inner_field" => $this->t]);
        $form->process(["a" => ["hello", "bye"]]);
        $a = $form->a;
        $this->assertEquals("a-1", $a->popEntry()->name);
        $this->assertEquals(["hello"], $a->data);
        $a->appendEntry("orange");
        $this->assertEquals(["hello", "orange"], $a->data);
        $this->assertEquals("a-1", $a[1]->name);
        $this->assertEquals('orange', $a->popEntry()->data);
        $this->assertEquals('a-0', $a->popEntry()->name);

        // This last one should throw an error
        $a->popEntry();
    }

    /**
     * @expectedException \WTForms\Exceptions\AssertionError
     * @expectedExceptionMessage You cannot have more than max_entries entries in the FieldList
     */
    public function testMinMaxEntries()
    {
        $form = new Form();
        $form->a = new FieldList(["inner_field" => $this->t, "min_entries" => 1, "max_entries" => 3]);
        $form->process([]);
        $a = $form->a;
        $this->assertEquals(1, count($a));
        $this->assertEquals(null, $a[0]->data);
        $big_input = ["foo", "flaf", "bar", "baz"];
        $form->process(["a" => $big_input]);
    }

    /**
     * @expectedException \WTForms\Exceptions\AssertionError
     * @expectedExceptionMessage You cannot have more than max_entries entries in the FieldList
     */
    public function testMinMaxEntriesForAppend()
    {
        $form = new Form();
        $form->a = new FieldList(["inner_field" => $this->t, "min_entries" => 1, "max_entries" => 3]);
        $form->process([
            "formdata" => [
                "a-0" => ["foo"],
                "a-1" => ["flaf"],
                "a-2" => ["bar"]
            ]
        ]);
        $a = $form->a;
        $this->assertEquals(["foo", "flaf", "bar"], $a->data);
        $a->appendEntry();
    }

    public function testValidators()
    {

        $form = new Form();
        $form->a = new FieldList(["inner_field" => $this->t, "validators" => [new Val]]);
        $fdata = [
            "a-0" => ["hello"],
            "a-1" => ["bye"],
            "a-2" => ["test3"]
        ];
        // Checking length validators working
        $form->process(["formdata" => $fdata]);
        $this->assertFalse($form->validate());
        $this->assertEquals(['too many'], $form->a->errors);

        $fdata["a-0"] = ["fail"];
        // Checking a value within
        $form->process(["formdata" => $fdata]);
        $this->assertFalse($form->validate());
        $this->assertEquals(["fail"], $form->a->errors);

        // Normal field validator still works
        $form->process(["formdata" => ["a-0" => [""]]]);
        $this->assertFalse($form->validate());
        $this->assertEquals([["This field is required."]], $form->a->errors);
    }

    /**
     * @expectedException \WTForms\Exceptions\TypeError
     * @expectedExceptionMessage FieldList does not accept any filters. Instead, define them on the enclosed field
     */
    public function testNoFilters()
    {
        $f = new FieldList([
            "inner_field" => $this->t,
            "filters"     => [
                function ($x) {
                    return $x;
                }
            ],
            "form"        => new Form(),
            "name"        => "foo"
        ]);
    }

    public function testProcessPrefilled()
    {
        $data = ["foo", "hi", "rawr"];
        $obj = (object)["a" => $data];
        $form = new Form();
        $form->a = new FieldList(["inner_field" => $this->t]);
        $form->process(["obj" => $obj]);
        $this->assertEquals(3, count($form->a->entries));

        // Pretend to submit the form unchanged
        $pdata = ["a-0" => ["foo"], "a-1" => ["hi"], "a-2" => ["rawr"]];
        $form->process(["formdata" => $pdata]);
        // check if data still the same
        $this->assertEquals(3, count($form->a->entries));
        $this->assertEquals($data, $form->a->data);
    }
}

class Val extends Validator
{
    public function __construct($message = "", array $options = [])
    {
        $this->message = $message;
    }

    function __invoke(Form $form, Field $field, $message = "")
    {
        if ($field->data && $field->data[0] == "fail") {
            throw new ValueError("fail");
        } elseif (count($field->data) > 2) {
            throw new ValueError("too many");
        }
    }

}