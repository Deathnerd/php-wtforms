<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 6/5/2016
 * Time: 4:30 PM
 */

namespace WTForms\Test\Fields;

use WTForms\Fields\Simple\HiddenField;
use WTForms\Form;

class HiddenFieldTest extends \PHPUnit_Framework_TestCase
{
  public function testHiddenField()
  {
    $form = new Form();
    $form->a = new HiddenField(["default" => "LE DEFAULT"]);
    $form->process([]);
    $this->assertEquals('<input id="a" name="a" type="hidden" value="LE DEFAULT">', "$form->a");
    $this->assertTrue($form->a->flags->hidden);
  }

}
