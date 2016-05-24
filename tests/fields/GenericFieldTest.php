<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 4/8/2016
 * Time: 3:43 PM
 */

namespace WTForms\Tests\Fields;


use WTForms\Fields\Core\StringField;
use WTForms\Form;
use WTForms\Validators\AnyOf;
use WTForms\Validators\InputRequired;

/**
 * @property StringField $a
 */
class GenericFieldTestForm extends Form
{
  /**
   * @inheritdoc
   */
  public function __construct(array $options = [])
  {
    parent::__construct($options);
    $this->a = new StringField(["attributes" => ["foo" => "bar"],
                                "render_kw"  => ["readonly" => true],
                                "validators" => [
                                    new InputRequired("This input is required, yo"),
                                    new AnyOf("You've gotta match these, guy %s", ["values" => [1, "foo", DIRECTORY_SEPARATOR]])
                                ]]);
    $this->process($options);
  }

}

class GenericFieldTest extends \PHPUnit_Framework_TestCase
{

  public function setUp()
  {
  }

  public function testProcessData()
  {
    $form = new GenericFieldTestForm();
    $form->a->processFormData([42]);
    $this->assertEquals(42, $form->a->data);
  }

  /**
   * Test whether meta is overridden from parent form's meta
   */
  public function testMetaAttribute()
  {
    $form = new GenericFieldTestForm();
    $field = $form->a;
    $this->assertEquals($form->a->meta, $field->meta);
  }

  public function testRenderKw()
  {
    $form = new GenericFieldTestForm(["a" => "hello"]);
    $output = $form->a->__invoke();
    $this->assertContains('id="a"', $output);
    $this->assertContains('type="text"', $output);
    $this->assertContains('value="hello"', $output);
    $this->assertContains('readonly', $output);
    $this->assertContains('foo="bar"', $output);
    $this->assertContains('name="a"', $output);

    $output = $form->a->__invoke(['foo' => 'baz']);
    $this->assertContains('id="a"', $output);
    $this->assertContains('type="text"', $output);
    $this->assertContains('value="hello"', $output);
    $this->assertContains('readonly', $output);
    $this->assertContains('foo="baz"', $output);
    $this->assertContains('name="a"', $output);

    $output = $form->a->__invoke(['foo' => 'baz', 'readonly' => false, 'other' => 'hello']);
    $this->assertContains('id="a"', $output);
    $this->assertContains('type="text"', $output);
    $this->assertContains('value="hello"', $output);
    $this->assertContains('foo="baz"', $output);
    $this->assertContains('name="a"', $output);
    $this->assertContains('other="hello"', $output);
    $this->assertNotContains('readonly', $output);
  }
}
