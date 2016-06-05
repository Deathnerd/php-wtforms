<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/17/2016
 * Time: 3:16 PM
 */

namespace WTForms\Tests\Fields;


use WTForms\Fields\Core\FieldList;
use WTForms\Fields\Core\StringField;
use WTForms\Form;
use WTForms\Validators\DataRequired;

class FieldListTest extends \PHPUnit_Framework_TestCase
{

  public function setUp()
  {
  }

  public function testForm()
  {
    $form = new Form();
    $form->a = new FieldList(["inner_field" => new StringField(["validators" => [new DataRequired()]])]);
    $data = ['foo', 'hi', 'rawr'];
    $form->process(["a" => $data]);
    $a = $form->a;
    $this->assertEquals("hi", $a->entries[1]->data);
    $this->assertEquals("a-1", $a->entries[1]->name);
    $this->assertEquals(count($data), count(array_intersect($a->data, $data)));
    $this->assertEquals(3, count($a->entries));

    $post_data = ["a-0" => ["bleh"],
                  "a-3" => ["yarg"],
                  "a-4" => [""],
                  "a-7" => ["mmm"]];
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
}