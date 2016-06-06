<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/19/2016
 * Time: 9:26 AM
 */

namespace WTForms\Tests\Fields;


use WTForms\Fields\Core\FormField;
use WTForms\Fields\Core\StringField;
use WTForms\Form;
use WTForms\Validators\DataRequired;

class F extends Form
{
  /**
   * Form constructor.
   *
   * @param array $options
   */
  public function __construct(array $options = [])
  {
    parent::__construct($options);
    $this->a = new StringField(["validators" => [new DataRequired()], "name" => "a"]);
    $this->b = new StringField(["name" => "b"]);
    $this->process($options);
  }

}

class FormFieldTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @var Form
   */
  public $f1;
  /**
   * @var Form
   */
  public $f2;

  public function setUp()
  {
    $this->f1 = new Form();
    $this->f1->a = new FormField(["form_class" => '\WTForms\Tests\Fields\F']);
    $this->f1->process([]);

    $this->f2 = new Form();
    $this->f2->a = new FormField(["form_class" => '\WTForms\Tests\Fields\F', "separator" => "::"]);
    $this->f2->process([]);
  }

  public function testFormData()
  {
    $this->f1->process(["formdata" => ["a-a" => ["moo"]]]);
    $this->assertEquals("a-a", $this->f1->a->form->a->name);
    $this->assertEquals("moo", $this->f1->a['a']->data);
    $this->assertEquals("", $this->f1->a['b']->data);
    $this->assertTrue($this->f1->validate());
  }

  public function testIteration()
  {
    $actual = [];
    foreach ($this->f1->a as $x) {
      $actual[] = $x->name;
    }
    $this->assertEquals(["a-a", "a-b"], $actual);
  }

  public function testObj()
  {
    $obj = (object)["a" => (object)["a" => "mmm"]];
    $form = $this->f1;
    $form->process(["obj" => $obj]);
    $this->assertEquals("mmm", $form->a->form->a->data);
    $this->assertNull($form->a->form->b->data);
    $obj_inner = (object)["a" => null, "b" => "rawr"];
    $obj2 = (object)["a" => $obj_inner];
    $form->populateObj($obj2);
    $this->assertTrue($obj2->a === $obj_inner);
    $this->assertEquals("mmm", $obj_inner->a);
    $this->assertNull($obj_inner->b);
  }

  public function testWidget()
  {
    $this->assertEquals(
        '<table id="a"><tr><th><label for="a-a">A</label></th><td><input id="a-a" name="a-a" type="text" value=""></td></tr><tr><th><label for="a-b">B</label></th><td><input id="a-b" name="a-b" type="text" value=""></td></tr></table>',
        $this->f1->a->__invoke()
    );
  }

  public function testSeparator()
  {
    $form = $this->f2;
    $form->process(["formdata" => ["a-a" => "fake", "a::a" => "real"]]);
    $this->assertEquals("a::a", $form->a->a->name);
    $this->assertEquals("real", $form->a->a->data);
    $this->assertTrue($form->validate());
  }

  /**
   * @expectedException \WTForms\Exceptions\TypeError
   * @expectedExceptionMessage FormField does not accept any validators. Instead, define them on the enclosed form.
   */
  public function testNoValidators()
  {
    (new FormField(["form_class" => '\WTForms\Tests\Fields\F', "validators" => [new DataRequired()]]));
  }

  /**
   * @expectedException \WTForms\Exceptions\TypeError
   * @expectedExceptionMessage FormField cannot take filters, as the encapsulated data is not mutable
   */
  public function testNoFilters()
  {
    (new FormField([
        "form_class" => '\WTForms\Tests\Fields\F',
        "filters"    => function ($x) {
          return $x;
        }]));
  }

  /**
   * TODO: THIS
   * @expectedException \WTForms\Exceptions\TypeError
   */
  /*public function testNoExtraValidators()
  {
    $form = new C();
    $form->validate();
  }*/
}

class C extends Form
{
  public function __construct(array $options = [])
  {
    parent::__construct($options);
    $this->a = new FormField(["form_class" => '\WTForms\Tests\Fields\F']);
    $this->process($options);
  }

  public function validate_a($form, $field)
  {
    return;
  }
}
