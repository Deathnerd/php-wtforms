<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/24/2016
 * Time: 3:01 PM.
 */

namespace WTForms\Tests\Widgets;

use PHPUnit\Framework\TestCase;
use WTForms\Widgets\Core\ListWidget;

class ListWidgetTest extends TestCase
{
  public function testBasic()
  {
    $field = new DummyField([
      'id' => 'hai',
    ]);
    $field->entries = [
      new DummyField(
        [
          'data'  => 'foo',
          'label' => 'lfoo',
        ]
      ),
      new DummyField(
        [
          'data'  => 'bar',
          'label' => 'lbar',
        ]
      ),
    ];
    $this->assertEquals('<ul id="hai">'.
        '<li>lfoo foo</li>'.
        '<li>lbar bar</li>'.
        '</ul>', (new ListWidget())->__invoke($field));
    $widget = new ListWidget('ol', false);
    $this->assertEquals('<ol id="hai">'.
        '<li>foo lfoo</li>'.
        '<li>bar lbar</li>'.
        '</ol>', $widget->__invoke($field));
  }
}
