<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/24/2016
 * Time: 3:58 PM
 */

namespace WTForms\Tests\Widgets;

use WTForms\Fields\Core\Field;
use WTForms\Fields\Core\Label;
use WTForms\Fields\Core\RadioField;
use WTForms\Form;
use WTForms\Widgets\Core\CheckboxInput;
use WTForms\Widgets\Core\FileInput;
use WTForms\Widgets\Core\HiddenInput;
use WTForms\Widgets\Core\Input;
use WTForms\Widgets\Core\Option;
use WTForms\Widgets\Core\PasswordInput;
use WTForms\Widgets\Core\RadioInput;
use WTForms\Widgets\Core\SubmitInput;
use WTForms\Widgets\Core\TextArea;
use WTForms\Widgets\Core\TextInput;
use WTForms\Widgets\Core\Widget;

class DummyField extends Field
{
  public function __construct(array $options = [], Form $form = null)
  {
    parent::__construct($options, $form);
    $this->label = $options['label'] ?: $this->label;
    $this->data = $options['data'] ?: $this->data;
    $this->type = $options['type'] ?: 'TextField';
    $this->id = $options['id'] ?: $this->id;
  }

  public function __toString()
  {
    return $this->data;
  }

  public function __invoke($options = [])
  {
    return $this->data;
  }

  public function __call($name, $arguments)
  {
    return $this->data;
  }
}

class BasicWidgetsTest extends \PHPUnit_Framework_TestCase
{
  private $field;

  public function testInputType()
  {
    $a = new Input();
    $this->assertAttributeEmpty("input_type", $a);
    $b = new Input("test");
    $this->assertEquals("test", $b->input_type);
  }

  public function testTextInput()
  {
    $actual = (new TextInput)->__invoke($this->field);
    $this->assertContains('id="id"', $actual);
    $this->assertContains('type="text"', $actual);
    $this->assertContains('value="foo"', $actual);
    $this->assertContains('name="bar"', $actual);
  }

  public function testPasswordInput()
  {
    $this->assertContains('type="password"', (new PasswordInput)->__invoke($this->field));
    $this->assertContains('value=""', (new PasswordInput)->__invoke($this->field));
    $this->assertContains('value="foo', (new PasswordInput(false))->__invoke($this->field));
  }

  public function testHiddenInput()
  {
    $this->assertContains('type="hidden"', (new HiddenInput)->__invoke($this->field));
    $this->assertContains('hidden', (new HiddenInput)->field_flags);
  }

  public function testCheckboxInput()
  {
    $actual = (new CheckboxInput)->__invoke($this->field, ["value" => "v"]);
    $this->assertContains('checked', $actual);
    $this->assertContains('id="id"', $actual);
    $this->assertContains('name="bar"', $actual);
    $this->assertContains('type="checkbox"', $actual);
    $this->assertContains('value="v"', $actual);
    $field2 = new DummyField(["data" => false]);
    $this->assertNotContains("checked", (new CheckboxInput)->__invoke($field2));
  }

  public function testTextArea()
  {
    $field = new DummyField(["data" => "hi<>bye"]);
    $field->name = "f";
    $this->assertEquals('<textarea id="" name="f">hi&lt;&gt;bye</textarea>', (new TextArea)->__invoke($field));
  }

  public function testFileInput()
  {
    $field = new DummyField();
    $field->name = "f";
    $this->assertEquals('<input id="" name="f" type="file">', (new FileInput)->__invoke($field));
  }

  public function testRadioInput()
  {
    $field = new DummyField(["data" => "foobar"]);
    $field->name = "f";
    $this->assertEquals('<input id="" name="f" type="radio" value="foobar">', (new RadioInput)->__invoke($field));
    $field->checked = true;
    $this->assertEquals('<input checked id="" name="f" type="radio" value="foobar">', (new RadioInput)->__invoke($field));
  }

  public function testSubmitInput()
  {
    $this->field->label = new Label($this->field->id, $this->field->label);
    $this->assertEquals('<input id="id" name="bar" type="submit" value="label">', (new SubmitInput)->__invoke($this->field));
  }

  /**
   * @expectedException \WTForms\NotImplemented
   */
  public function testWidgetNotImplemented()
  {
    (new Widget)->__invoke($this->field);
  }

  public function testOption()
  {
    $field = new DummyField();
    $field->value = "foobar";
    $field->label = new Label("foobar", "baz");
    $field->checked = false;
    $this->assertEquals('<option value="foobar">baz</option>', (new Option)->__invoke($field));
    $field->checked = true;
    $this->assertEquals('<option selected value="foobar">baz</option>', (new Option)->__invoke($field));
    $this->assertEquals('<option data-baz-bar="ohai" selected value="foobar">baz</option>', (new Option)->__invoke($field, ["data_baz-bar" => "ohai"]));;

  }

  protected function setUp()
  {
    $this->field = new DummyField(['data' => 'foo', 'name' => 'bar', 'label' => 'label', 'id' => 'id']);
  }
}
