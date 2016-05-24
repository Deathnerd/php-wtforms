<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/24/2016
 * Time: 5:28 PM
 */

namespace WTForms\Tests\Widgets;


use WTForms\Fields\Core\Field;
use WTForms\Form;
use WTForms\Widgets\HTML5\NumberInput;
use WTForms\Widgets\HTML5\RangeInput;

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

class HTML5Test extends \PHPUnit_Framework_TestCase
{
  public $field;

  public function testNumber()
  {
    $i1 = new NumberInput(['step' => 'any']);
    $this->assertEquals('<input id="id" name="bar" step="any" type="number" value="42">', $i1($this->field));

    $i2 = new NumberInput(['step' => 2]);
    $this->assertEquals('<input id="id" name="bar" step="3" type="number" value="42">', $i2($this->field, ["step" => 3]));

    $i3 = new NumberInput(['min' => 10]);
    $this->assertEquals('<input id="id" min="10" name="bar" type="number" value="42">', $i3($this->field));
    $this->assertEquals('<input id="id" min="5" name="bar" type="number" value="42">', $i3($this->field, ["min" => 5]));

    $i4 = new NumberInput(["max" => 100]);
    $this->assertEquals('<input id="id" max="100" name="bar" type="number" value="42">', $i4($this->field));
    $this->assertEquals('<input id="id" max="50" name="bar" type="number" value="42">', $i4($this->field, ['max' => 50]));
  }

  public function testRange()
  {
    $i1 = new RangeInput(["step" => "any"]);
    $this->assertEquals('<input id="id" name="bar" step="any" type="range" value="42">', $i1($this->field));
    $i2 = new RangeInput(['step' => 2]);
    $this->assertEquals('<input id="id" name="bar" step="3" type="range" value="42">', $i2($this->field, ['step' => 3]));
  }

  protected function setUp()
  {
    $this->field = new DummyField(['data' => '42', 'name' => 'bar', 'id' => 'id']);
  }

}
