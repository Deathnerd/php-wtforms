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
  public function __construct(array $options = [])
  {
    parent::__construct($options);
    $this->label = array_key_exists('label', $options) ? $options['label'] : $this->label;
    $this->data = array_key_exists('data', $options) ? $options['data'] : $this->data;
    $this->type = array_key_exists('type', $options) ? $options['type'] : 'TextField';
    $this->id = array_key_exists('id', $options) ? $options['id'] : $this->id;
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
    $this->assertEquals('<input id="id" name="bar" type="text" value="foo">', (new TextInput)->__invoke($this->field));
  }

  public function testPasswordInput()
  {
    $this->assertEquals('<input id="id" name="bar" type="password" value="">', (new PasswordInput)->__invoke($this->field));
    $this->assertEquals('<input id="id" name="bar" type="password" value="foo">', (new PasswordInput(false))->__invoke($this->field));
  }

  public function testHiddenInput()
  {
    $this->assertEquals('<input id="id" name="bar" type="hidden" value="foo">', (new HiddenInput)->__invoke($this->field));
    $this->assertContains('hidden', (new HiddenInput)->field_flags);
  }

  public function testCheckboxInput()
  {
    $this->assertEquals('<input checked id="id" name="bar" type="checkbox" value="v">', (new CheckboxInput)->__invoke($this->field, ["value" => "v"]));
    $field2 = new DummyField(["data" => false]);
    $this->assertEquals('<input id="" name="" type="checkbox" value="">', (new CheckboxInput)->__invoke($field2));
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
   * @expectedException \WTForms\Exceptions\NotImplemented
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
