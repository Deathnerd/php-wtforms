<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 4/8/2016
 * Time: 4:51 PM
 */

namespace WTForms\Tests\Fields;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;
use WTForms\Fields\Core\_Option;
use WTForms\Form;
use WTForms\Forms;
use WTForms\Tests\SupportingClasses\AnnotatedHelper;
use WTForms\Tests\SupportingClasses\Helper;
use WTForms\Widgets\Core\Option;
use WTForms\Widgets\Core\TextInput;

class SelectFieldTest extends \PHPUnit_Framework_TestCase
{
  protected $helper;
  protected $annotated_helper;
  protected $registry;
  protected $reader;
  /**
   * @var Form
   */
  protected $form;

  public function setUp()
  {
    $this->reader = new FileCacheReader(
        new AnnotationReader(),
        __DIR__ . "/../runtime",
        $debug = true
    );
    $this->registry = new AnnotationRegistry();
    $this->helper = new Helper;
    $this->annotated_helper = new AnnotatedHelper;
    Forms::init($this->reader, $this->registry);
    $this->form = Forms::create($this->annotated_helper);
  }

  public function testDefaults()
  {
    $this->assertEquals('a', $this->form->select_a->data);
    $this->assertEquals(null, $this->form->select_b->data);
    $this->assertEquals(false, $this->form->validate());
    $this->assertEquals('<select id="select_a" name="select_a"><option value="a" selected>hello</option><option value="btest">bye</option></select>', $this->form->select_a->__invoke());
    $this->assertEquals('<select id="select_b" name="select_b"><option value="1">Item 1</option><option value="2">Item 2</option></select>', $this->form->select_b->__invoke());
  }

  public function testWithData()
  {
    $form = Forms::create($this->annotated_helper, ["select_a" => ["btest"]]);
    $this->assertEquals("btest", $form->select_a->data);
    $this->assertEquals('<select id="select_a" name="select_a"><option value="a">hello</option><option value="btest" selected>bye</option></select>', $form->select_a->__invoke());
  }

  public function testIterableOptions()
  {
    $first_option = $this->form->select_a->options[0];
    $this->assertTrue($first_option instanceof _Option);
    $actual = [];
    foreach ($this->form->select_a->options as $option) {
      /**
       * @var $option _Option
       */
      $actual[] = $option->__toString();
    }
    $expected = ['<option value="a" selected>hello</option>', '<option value="btest">bye</option>'];
    $this->assertTrue(count(array_intersect($actual, $expected)) == 2);
    $this->assertTrue($first_option->widget instanceof Option);
    $this->assertTrue($this->form->select_b->options[0]->widget instanceof TextInput);
    $this->assertEquals('<option disabled value="a" selected>hello</option>', $first_option(["disabled" => true]));
  }

  public function testDefaultCoerce()
  {
    // TODO THIS
  }
}
