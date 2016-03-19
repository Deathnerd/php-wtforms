<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/18/2016
 * Time: 9:09 PM
 */

namespace WTForms\Tests\Fields;


use WTForms\Fields\Core\Label;

class LabelTest extends \PHPUnit_Framework_TestCase
{
  public function testDefaultLabelOptions()
  {
    $label = new Label("foobar", "Foo Bar");
    $this->assertEquals('<label for="foobar">Foo Bar</label>', $label());
  }

  public function testLabelInvoke()
  {
    $label = new Label("foobar", "Foo Bar");
    $expected = '<label for="foobar" class="form form-label" baz data-color="red" id="foobar_label">Shazbot</label>';
    $actual = $label("Shazbot", ["class"      => ["form", "form-label"],
                                 "baz"        => true,
                                 "data-color" => "red",
                                 "id"         => "foobar_label"]);
    $this->assertEquals($expected, $actual);
  }
}
