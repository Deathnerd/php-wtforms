<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/15/2016
 * Time: 9:11 PM
 */

namespace WTForms\Tests\Common;
require_once(__DIR__ . '/../../vendor/autoload.php');

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;
use WTForms\Fields\Core\Field;
use WTForms\Fields\Core\StringField;
use WTForms\Forms;
use WTForms\Tests\SupportingClasses\AnnotatedHelper;
use WTForms\Tests\SupportingClasses\Helper;

class FormsTest extends \PHPUnit_Framework_TestCase
{
  protected $helper;
  protected $annotated_helper;
  protected $registry;
  protected $reader;
  
  public function setUp()
  {
    $this->reader = new FileCacheReader(
        new AnnotationReader(),
        __DIR__ . "/../runtime",
        $debug = true
    );
    $this->registry = new AnnotationRegistry();
    $this->registry->registerFile(__DIR__ . "/../supporting_classes/Foo.php");
    $this->registry->registerFile(__DIR__ . "/../supporting_classes/Bar.php");
    $this->registry->registerFile(__DIR__ . "/../../src/annotations/Extend.php");
    $this->registry->registerFile(__DIR__ . "/../../src/annotations/Field.php");
    $this->registry->registerFile(__DIR__ . "/../../src/annotations/Form.php");
    $this->helper = new Helper;
    $this->annotated_helper = new AnnotatedHelper;
    Forms::init($this->reader, $this->registry);
  }

  /**
   * Should throw an error
   * @expectedException \Doctrine\Common\Annotations\AnnotationException
   */
  public function testCreateNonAnnotated()
  {
    $form = Forms::create($this->helper);
  }

  /**
   * Test for form annotation overrides
   */
  public function testCreateFormAnnotation()
  {
    $form = Forms::create($this->annotated_helper);
    $this->assertEquals('foo', $form->prefix);
    $this->assertEquals('foo-first_name', $form['first_name']->name);
    $this->assertAttributeInstanceOf('WTForms\Tests\SupportingClasses\FooMeta', 'meta', $form);
    $this->assertEquals(false, $form->csrf);
  }

  /**
   * Test for field annotations
   */
  public function testCreateFieldAnnotation()
  {
    $form = Forms::create($this->annotated_helper);
    $this->assertNotEmpty($form->fields);
    $this->assertTrue($form['first_name'] instanceof Field && $form['first_name'] instanceof StringField);
    $this->assertEquals('<label for="fname">First Name</label>', $form['first_name']->label("First Name"));
  }

  /**
   * Test for populated by Formdata
   */
  public function testPopulateByFormdata()
  {
    $form = Forms::create($this->annotated_helper, ['first_name' => "Chester", 'last_name' => "Tester"]);
    $this->assertEquals('Chester', $form['first_name']->value);
    $this->assertNull($form['middle_name']);
  }

  /**
   * Test for populated by Data
   */
  public function testPopulateByData()
  {
    $form = Forms::create($this->annotated_helper, [], ['first_name' => "Chester", 'last_name' => "Tester"]);
    $this->assertEquals('Chester', $form['first_name']->value);
    $this->assertNull($form['middle_name']);
  }

  /**
   * Test for populated by Object
   */
  public function testPopulateByObject()
  {
    $form = Forms::create($this->annotated_helper, [], [], (object) ['first_name'=>"Chester", 'last_name'=>"Tester"]);
    $this->assertEquals('Chester', $form['first_name']->value);
    $this->assertNull($form['middle_name']);
  }
}
