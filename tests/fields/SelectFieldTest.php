<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 4/8/2016
 * Time: 4:51 PM
 */

namespace WTForms\Tests\Fields;


use WTForms\Exceptions\TypeError;
use WTForms\Exceptions\ValueError;
use WTForms\Fields\Core\_Option;
use WTForms\Fields\Core\SelectField;
use WTForms\Fields\Core\SelectFieldBase;
use WTForms\Form;
use WTForms\Widgets\Core\Option;
use WTForms\Widgets\Core\TextInput;

class SelectFieldBaseTestClass extends SelectFieldBase
{

}

class SelectFieldTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @var Form
   */
  public $form;

  protected function setUp()
  {
    $form = new Form();
    $form->a = new SelectField(["choices" => [["a", "hello"], ["btest", "bye"]],
                                "default" => "a"]);
    $form->b = new SelectField(["choices"       => [[1, "Item 1"], [2, "Item 2"]],
                                "coerce"        => function ($x) {
                                  if (!is_numeric($x)) {
                                    throw new ValueError;
                                  }

                                  return intval($x);
                                },
                                "option_widget" => new TextInput
    ]);
    $form->c = new SelectField(["default" => "c"]);
    $form->d = new SelectField(["choices" => [["d", "hiya"]],
                                "coerce"  => function ($x) {
                                  if ($x == "hiya") {
                                    throw new TypeError;
                                  }

                                  return $x;
                                }]);
    $form->process([]);
    $this->form = $form;
  }

  public function testDefaults()
  {
    $this->assertEquals('a', $this->form->a->data);
    $this->assertEquals(null, $this->form->b->data);
    $this->assertFalse($this->form->validate());
    $this->assertEquals('<select id="a" name="a"><option selected value="a">hello</option><option value="btest">bye</option></select>', $this->form->a->__toString());
    $this->assertEquals($this->form->a->__toString(), $this->form->a->__invoke());
    $this->assertEquals($this->form->a->__toString(), "{$this->form->a}");
    $this->assertEquals('<select id="b" name="b"><option value="1">Item 1</option><option value="2">Item 2</option></select>', $this->form->b->__toString());
    $this->assertEquals(0, count($this->form->c->options));
    $this->form->process(["d" => "hiya"]);
    $this->assertFalse($this->form->validate());
  }

  public function testWithData()
  {
    $this->form->process(["formdata" => ["a" => ["btest"]]]);
    $this->assertEquals("btest", $this->form->a->data);
    $this->assertEquals('<select id="a" name="a"><option value="a">hello</option><option selected value="btest">bye</option></select>', $this->form->a->__toString());
  }

  public function testValueCoercion()
  {
    $this->form->process(["formdata" => ["b" => ["2"]]]);
    $this->assertTrue(2 === $this->form->b->data);
    $this->assertTrue($this->form->b->validate($this->form));

    $this->setUp();
    $this->form->process(["formdata" => ["b" => ["b"]]]);
    $this->assertEquals(null, $this->form->b->data);
    $this->assertFalse($this->form->b->validate($this->form));
  }

  public function testIterableOptions()
  {
    $options = $this->form->a->options;
    $first_option = $options[0];
    $this->assertTrue($first_option instanceof _Option);
    $text_options = [];
    foreach ($this->form->a->options as $option) {
      $text_options[] = "$option";
    }
    $this->assertEquals(['<option selected value="a">hello</option>', '<option value="btest">bye</option>'], $text_options);
    $this->assertTrue($first_option->widget instanceof Option);
    $this->assertTrue($this->form->b->options[0]->widget instanceof TextInput);
    $this->assertEquals('<option disabled selected value="a">hello</option>', $first_option(["disabled" => true]));
  }

  /**
   * @expectedException \WTForms\Exceptions\NotImplemented
   */
  public function testSelectFieldBase()
  {
    $field = new SelectFieldBaseTestClass();
    $this->assertEmpty($field->value);
    $field->getChoices();
  }
}