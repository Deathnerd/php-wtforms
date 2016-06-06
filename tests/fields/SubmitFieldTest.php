<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 6/3/2016
 * Time: 4:02 PM
 */

namespace WTForms\Tests\Fields;


use WTForms\Fields\Simple\SubmitField;
use WTForms\Form;

class SubmitFieldTest extends \PHPUnit_Framework_TestCase
{
  public function testSubmitField()
  {
    $form = new Form();
    $form->a = new SubmitField(["label" => "Label"]);
    $form->process([]);
    $this->assertEquals('<input id="a" name="a" type="submit" value="Label">', "$form->a");
  }

}
