<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/17/2016
 * Time: 3:16 PM
 */

namespace WTForms\Tests\Fields;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;
use WTForms\Annotations\Fields\Core\FieldList;
use WTForms\Annotations\Fields\Core\StringField;
use WTForms\Annotations\Form;
use WTForms\Annotations\Validators\DataRequired;
use WTForms\Forms;
use WTForms\Tests\SupportingClasses\AnnotatedHelper;
use WTForms\Tests\SupportingClasses\Helper;

/**
 * @Form
 */
class TestForm
{
  /**
   * @var FieldList
   * @FieldList(inner_field=@StringField(validators={@DataRequired}))
   */
  public $a;
}

/**
 * @Form
 */
class FChild
{
  /**
   * @var string
   * @StringField(validators={@DataRequired})
   */
  public $a;
}

/**
 * @Form
 */
class TestEnclosedSubForm
{
  /**
   * @var Form
   * TODO: Enclosed SubForm test
   */
  public $a;
}

class FieldListTest extends \PHPUnit_Framework_TestCase
{
  protected $helper;
  protected $annotated_helper;
  protected $registry;
  protected $reader;

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
  }

  public function testForm()
  {
    $data = ['foo', 'hi', 'rawr'];
    $this->form = Forms::create(new TestForm, [], ["a" => $data]);
    $a = $this->form->a;
    $this->assertEquals("hi", $a->entries[1]->data);
    $this->assertEquals("a-1", $a->entries[1]->name);
    $this->assertEquals(count($data), count(array_intersect($a->data, $data)));
    $this->assertEquals(3, count($a->entries));

    $post_data = ["a-0" => ["bleh"],
                  "a-3" => ["yarg"],
                  "a-4" => [""],
                  "a-7" => ["mmm"]];
    $this->form = Forms::create(new TestForm, $post_data);
    $this->assertEquals(4, count($this->form->a->entries));
    $this->assertEquals(["bleh", "yarg", "", "mmm"], $this->form->a->data);
    $this->assertFalse($this->form->validate());


    $this->form = Forms::create(new TestForm, $post_data, ["a" => $data]);
    $this->assertEquals(["bleh", "yarg", "", "mmm"], $this->form->a->data);
    $this->assertFalse($this->form->validate());

    // test for formdata precedence
    $post_data = ["a-0" => ["a"], "a-1" => ["b"]];
    $this->form = Forms::create(new TestForm, $post_data, ["a" => $data]);
    $this->assertEquals(2, count($this->form->a->entries));
    $this->assertEquals($this->form->a->data, ["a", "b"]);
    $expected = [];
    // Testing iteration
    foreach ($this->form->a as $entry) {
      $expected[] = $entry;
    }
    $this->assertEquals(count($expected), count($this->form->a->entries));
    // Testing countable
    $this->assertEquals(count($this->form->a), 2);
  }
}
