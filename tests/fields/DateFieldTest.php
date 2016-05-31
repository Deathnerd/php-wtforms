<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/30/2016
 * Time: 7:18 PM
 */

namespace WTForms\Tests\Fields;


use WTForms\Fields\Core\DateField;
use WTForms\Fields\Core\DateTimeField;
use WTForms\Form;

class DateFieldTest extends \PHPUnit_Framework_TestCase
{
  public function testDateField()
  {
    $form = new Form();

    $form->dfield = new DateField(["name" => "dfield", "default" => new \DateTime('12/12/2012')]);
    $form->process([]);
    $this->assertEquals("Y-m-d", $form->dfield->format);
    $this->assertTrue($form->dfield instanceof DateTimeField);
    $this->assertEquals('<input id="dfield" name="dfield" type="text" value="2012-12-12">', "$form->dfield");
  }
}
