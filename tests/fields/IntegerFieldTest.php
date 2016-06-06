<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 6/6/2016
 * Time: 4:27 PM
 */

namespace WTForms\Tests\Fields;


use WTForms\Fields\Core\IntegerField;
use WTForms\Form;

class IntegerFieldTest extends \PHPUnit_Framework_TestCase
{
  public function testIntegerField()
  {
    $form = new Form();
    $form->a = new IntegerField();
    $form->b = new IntegerField(["default" => 48]);
    $form->process(["formdata" => ["a" => ["v"], "b" => ["-15"]]]);
    $this->assertNull($form->a->data);
    $this->assertEquals(["v"], $form->a->raw_data);
    $this->assertEquals('<input id="a" name="a" type="text" value="v">', "$form->a");
    $this->assertEquals(-15, $form->b->data);
    $this->assertEquals('<input id="b" name="b" type="text" value="-15">', "$form->b");
    $this->assertFalse($form->a->validate($form));
    $this->assertTrue($form->b->validate($form));

    $form->process(["formdata" => ["a" => [], "b" => [""]]]);
    $this->assertNull($form->a->data);
    $this->assertEquals([], $form->a->raw_data);
    $this->assertNull($form->b->data);
    $this->assertEquals([""], $form->b->raw_data);
    $this->assertFalse($form->validate());
    $this->assertEquals(1, count($form->b->process_errors));
    $this->assertEquals(1, count($form->b->errors));

    $form = new Form();
    $form->a = new IntegerField();
    $form->b = new IntegerField(["default" => 48]);
    $form->process(["b" => 9]);
    $this->assertEquals(9, $form->b->data);
    $this->assertEquals('', $form->a->value);
    $this->assertEquals('9', $form->b->value);
  }

}
