<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/24/2016
 * Time: 3:52 PM
 */

namespace WTForms\Tests\Widgets;

use WTForms\widgets\core\TableWidget;

class TableWidgetTest extends \PHPUnit_Framework_TestCase
{
  public function testBasic()
  {
    $field = new DummyField(["id" => "hai"]);
    $field->entries = [
        new DummyField([
            "data" => "hidden1",
            "type" => 'HiddenField'
        ]),
        new DummyField([
            "data"  => "foo",
            "label" => "lfoo"
        ]),
        new DummyField([
            "data"  => "bar",
            "label" => "lbar"
        ]),
        new DummyField([
            "data" => "hidden2",
            "type" => 'HiddenField'
        ])
    ];
    $this->assertEquals('<table id="hai"><tr><th>lfoo</th><td>hidden1foo</td></tr><tr><th>lbar</th><td>bar</td></tr></table>hidden2', (new TableWidget())->__invoke($field));
  }
}
