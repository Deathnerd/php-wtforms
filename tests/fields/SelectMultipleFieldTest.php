<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/30/2016
 * Time: 3:52 PM
 */

namespace WTForms\Tests\Fields;


use WTForms\Exceptions\ValueError;
use WTForms\Fields\Core\SelectMultipleField;
use WTForms\Form;

class SelectMultipleFieldTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @var Form
   */
  public $form;

  protected function setUp()
  {
    $form = new Form();
    $form->a = new SelectMultipleField(["choices" => [["a", "hello"], ["b", "bye"], ["c", "something"]],
                                        "default" => ["a"]]);
    $form->b = new SelectMultipleField(["choices" => [[1, "A"], [2, "B"], [3, "C"]],
                                        "default" => ["1", "3"],
                                        "coerce"  => function ($x) {
                                          if (!is_numeric($x)) {
                                            throw new ValueError;
                                          }

                                          return intval($x);
                                        }]);
    $form->process([]);
    $this->form = $form;
  }

  public function testDefaults()
  {
    $this->assertEquals(['a'], $this->form->a->data);
    $this->assertEquals([1, 3], $this->form->b->data);
    // Test for possible regression with null data
    $this->form->a->data = null;
    $this->assertTrue($this->form->validate());
    $choices = [];
    foreach ($this->form->a->getChoices() as $choice) {
      $choices[] = $choice;
    }
    $this->assertEquals([["a", "hello", false], ["b", "bye", false], ["c", "something", false]], $choices);
  }

  public function testWithData()
  {
    $this->form->process(["formdata" => ["a" => ["a", "c"]]]);
    $this->assertEquals(["a", "c"], $this->form->a->data);
    $choices = [];
    foreach ($this->form->a->getChoices() as $choice) {
      $choices[] = $choice;
    }
    $this->assertEquals([['a', 'hello', true], ['b', 'bye', false], ['c', 'something', true]], $choices);
    $this->assertEmpty($this->form->b->data);

    $this->form->process(["formdata" => ["b" => ["1", "2"]]]);
    $this->assertEquals([1, 2], $this->form->b->data);
    $this->assertTrue($this->form->validate());

    $this->form->process(["formdata" => ["b" => ["1", "2", "4"]]]);
    $this->assertEquals([1, 2, 4], $this->form->b->data);
    $this->assertFalse($this->form->validate());

    $this->form->process(["b" => "12345"]);
    $this->assertTrue($this->form->validate());
    $this->assertNull($this->form->b->data);
  }

  public function testCoerceFail()
  {
    unset($this->form->a);
    $this->form->process(["b" => ["a"]]);
    $this->assertTrue($this->form->validate());
    $this->assertEquals(null, $this->form->b->data);
    $this->form->process(["formdata" => ["b" => ["fake"]]]);
    $this->assertFalse($this->form->validate());
    $this->assertEquals([1, 3], $this->form->b->data);
  }
}
