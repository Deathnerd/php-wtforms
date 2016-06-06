<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/30/2016
 * Time: 5:04 PM
 */

namespace WTForms\Tests\Fields;


use WTForms\Exceptions\TypeError;
use WTForms\Fields\Core\RadioField;
use WTForms\Form;

class RadioFieldTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @var Form
   */
  public $form;

  protected function setUp()
  {
    $form = new Form();
    $form->a = new RadioField(["choices" => [["a", "hello"], ["b", "bye"]],
                               "default" => "a"]);
    $form->b = new RadioField(["choices" => [[1, "Item 1"], [2, "Item 2"]],
                               "coerce"  => function ($x) {
                                 if (!is_numeric($x)) {
                                   throw new TypeError;
                                 }

                                 return intval($x);
                               }]);
    $form->process([]);
    $this->form = $form;
  }

  public function testRadioField()
  {
    $this->assertEquals('a', $this->form->a->data);
    $this->assertEquals(null, $this->form->b->data);
    $this->assertFalse($this->form->validate());
    $expected = '<ul id="a"><li><input checked id="a-0" name="a" type="radio" value="a"> <label for="a-0">hello</label></li><li><input id="a-1" name="a" type="radio" value="b"> <label for="a-1">bye</label></li></ul>';
    $this->assertEquals($expected, "{$this->form->a}");
    $expected = '<ul id="b"><li><input id="b-0" name="b" type="radio" value="1"> <label for="b-0">Item 1</label></li><li><input id="b-1" name="b" type="radio" value="2"> <label for="b-1">Item 2</label></li></ul>';
    $this->assertEquals($expected, "{$this->form->b}");
    $options = [];
    foreach ($this->form->a->options as $option) {
      $options[] = "$option";
    }
    $this->assertEquals(['<input checked id="a-0" name="a" type="radio" value="a">', '<input id="a-1" name="a" type="radio" value="b">'], $options);
  }

  public function testTextCoercion()
  {
    # Regression test for text coercsion scenarios where the value is a boolean.
    $this->form->a = new RadioField(["choices" => [[true, "yes"], [false, "no"]],
                                     "coerce"  => function ($x) {
                                       if ($x == "false") {
                                         return false;
                                       }

                                       return boolval($x);
                                     }]);
    $this->form->process([]);
    $expected = '<ul id="a"><li><input id="a-0" name="a" type="radio" value="true"> <label for="a-0">yes</label></li><li><input checked id="a-1" name="a" type="radio" value="false"> <label for="a-1">no</label></li></ul>';
    $this->assertEquals($expected, "{$this->form->a}");
  }
}
