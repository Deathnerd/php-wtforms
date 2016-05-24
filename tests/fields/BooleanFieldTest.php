<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/27/2016
 * Time: 4:14 PM
 */

namespace WTForms\Tests\Fields;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;
use WTForms\Forms;
use WTForms\Tests\SupportingClasses\AnnotatedHelper;

/*class BooleanFieldTest extends \PHPUnit_Framework_TestCase
{
  protected $annotated_helper;
  protected $registry;
  protected $reader;
  protected $object;

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
    $this->annotated_helper = new AnnotatedHelper;
    Forms::init($this->reader, $this->registry);
    $this->object = new \stdClass();
    $this->object->bool1 = null;
    $this->object->bool2 = true;
  }

  public function testDefaults()
  {
    $form = Forms::create($this->annotated_helper);
    $this->assertEquals([], $form['bool1']->raw_data);
    $this->assertEquals(false, $form['bool1']->data);
    $this->assertEquals(true, $form['bool2']->data);
  }

  public function testRendering()
  {
    $form = Forms::create($this->annotated_helper, ["bool2" => "x"]);
    $this->assertEquals('<input id="bool1" type="checkbox" value="y" name="bool1">', $form['bool1']->__invoke());
    $this->assertEquals('<input id="bool2" type="checkbox" value="x" checked name="bool2">', $form['bool2']->__invoke());
    $this->assertEquals(["x"], $form['bool2']->raw_data);
  }

  public function testWithPostData()
  {
    $form = Forms::create($this->annotated_helper, ["bool1" => ['a']]);
    $this->assertEquals(['a'], $form['bool1']->raw_data);
    $this->assertEquals(true, $form['bool1']->data);

    $form = Forms::create($this->annotated_helper, ["bool1" => ["false"], "bool2" => ["false"]]);
    $this->assertEquals(false, $form['bool1']->data);
    $this->assertEquals(true, $form['bool2']->data);
  }

  public function testWithObjectData()
  {
    $form = Forms::create($this->annotated_helper, [], [], $this->object);
    $this->assertEquals(false, $form['bool1']->data);
    $this->assertEquals(true, empty($form['bool1']->raw_data));
    $this->assertEquals(true, $form['bool2']->data);
  }

  public function testWithObjectAndPostData()
  {
    $form = Forms::create($this->annotated_helper, ["bool1"=>["y"]], [], $this->object);
    $this->assertEquals(true, $form['bool1']->data);
    $this->assertEquals(false, $form['bool2']->data);
  }
}*/
