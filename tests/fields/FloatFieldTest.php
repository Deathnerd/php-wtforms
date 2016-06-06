<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/30/2016
 * Time: 6:53 PM
 */

namespace WTForms\Tests\Fields;


use WTForms\Fields\Core\FloatField;
use WTForms\Form;

class FloatFieldTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @var Form
   */
  public $form;

  protected function setUp()
  {
    $form = new Form();
    $form->a = new FloatField();
    $form->b = new FloatField(["default" => 48.0]);
    $this->form = $form;
  }

  public function testFloatField()
  {
    $this->form->process(["formdata" => ["a" => ["v"], "b" => ["-15.0"]]]);
    $this->assertNull($this->form->a->data);
    $this->assertEquals(["v"], $this->form->a->raw_data);
    $this->assertEquals('<input id="a" name="a" type="text" value="v">', "{$this->form->a}");

    $this->assertEquals(-15.0, $this->form->b->data);
    $this->assertEquals('<input id="b" name="b" type="text" value="-15.0">', "{$this->form->b}");
    $this->assertFalse($this->form->a->validate($this->form));
    $this->assertTrue($this->form->b->validate($this->form));

    $this->form->process(["formdata" => ["a" => [], "b" => [""]]]);
    $this->assertNull($this->form->a->data);
    $this->assertEquals('', $this->form->a->value);

    $this->assertEquals(null, $this->form->b->data);
    $this->assertEquals([""], $this->form->b->raw_data);
    $this->assertFalse($this->form->validate());

    $this->assertEquals(1, count($this->form->b->process_errors));
    $this->assertEquals(1, count($this->form->b->errors));
    $this->setUp();
    $this->form->process(["b" => 9.1]);
    $this->assertEquals(9.1, $this->form->b->data);
    $this->assertEquals("9.1", $this->form->b->value);
  }
}
