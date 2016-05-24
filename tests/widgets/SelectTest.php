<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/24/2016
 * Time: 5:14 PM
 */

namespace WTForms\Tests\Widgets;


use WTForms\Fields\Core\Field;
use WTForms\Form;
use WTForms\Widgets\Core\Select;

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

class SelectTest extends \PHPUnit_Framework_TestCase
{

  public $field;

  protected function setUp()
  {
    $this->field = new DummyField([
        "data" => [
            ["foo", "lfoo", true],
            ["bar", "lbar", false]
        ]
    ]);
    $this->field->name = "f";
  }

  public function testBasic()
  {
    $this->assertEquals('<select id="" name="f"><option selected value="foo">lfoo</option><option value="bar">lbar</option></select>', (new Select())->__invoke($this->field));
    $this->assertEquals('<select id="" multiple name="f"><option selected value="foo">lfoo</option><option value="bar">lbar</option></select>', (new Select())->__invoke($this->field, ['multiple' => true]));
  }

  public function testRenderOption()
  {
    $this->assertEquals('<option value="bar">foo</option>', (new Select())->renderOption('bar', 'foo', false));
    $this->assertEquals('<option selected value="true">foo</option>', (new Select())->renderOption(true, 'foo', true));
  }
}
