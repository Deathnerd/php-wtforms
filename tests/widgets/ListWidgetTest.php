<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/24/2016
 * Time: 3:01 PM
 */

namespace WTForms\Tests\Widgets;


use WTForms\Fields\Core\Field;
use WTForms\Fields\Core\FieldList;
use WTForms\Fields\Core\StringField;
use WTForms\Form;
use WTForms\Widgets\Core\ListWidget;


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

class ListWidgetTest extends \PHPUnit_Framework_TestCase
{
  public function testBasic()
  {
    $field = new DummyField([
        "id" => "hai"
    ]);
    $field->entries = [
        new DummyField(
            [
                "data"  => "foo",
                "label" => "lfoo"
            ]
        ),
        new DummyField(
            [
                "data"  => "bar",
                "label" => "lbar"
            ]
        )
    ];
    $this->assertEquals('<ul id="hai"><li>lfoo foo</li><li>lbar bar</li></ul>', (new ListWidget())->__invoke($field));
    $widget = new ListWidget("ol", false);
    $this->assertEquals('<ol id="hai"><li>foo lfoo</li><li>bar lbar</li></ol>', $widget->__invoke($field));
  }
}
