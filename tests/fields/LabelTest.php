<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/18/2016
 * Time: 9:09 PM
 */

namespace WTForms\Tests\Fields;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;
use WTForms\Fields\Core\Label;
use WTForms\Forms;
use WTForms\Tests\SupportingClasses\AnnotatedHelper;
use WTForms\Tests\SupportingClasses\Helper;

/*class LabelTest extends \PHPUnit_Framework_TestCase
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
    $this->helper = new Helper;
    $this->annotated_helper = new AnnotatedHelper;
    Forms::init($this->reader, $this->registry);
  }

  public function testDefaultLabelOptions()
  {
    $label = new Label("foobar", "Foo Bar");
    $this->assertEquals('<label for="foobar">Foo Bar</label>', $label());
  }

  public function testLabelInvokeWithOptions()
  {
    $label = new Label("foobar", "Foo Bar");
    $actual = $label("Shazbot", ["class"      => ["form", "form-label"],
                                 "baz"        => true,
                                 "data-color" => "red",
                                 "id"         => "foobar_label"]);
    $expected = '<label for="foobar" class="form form-label" baz data-color="red" id="foobar_label">Shazbot</label>';
    $this->assertEquals($expected, $actual);
  }

  public function testLabelToString()
  {
    $label = new Label("foobar", "Foo Bar");
    $actual = "$label";
    $expected = '<label for="foobar">Foo Bar</label>';
    $this->assertEquals($expected, $actual);
    $this->assertEquals($expected, $label->__toString());
  }

  public function testAutoLabel()
  {
    $form = Forms::create($this->annotated_helper);
    $this->assertEquals('<label for="fname">First Name</label>', $form['first_name']->label());
    $this->assertEquals('<label for="last_name">Last Name</label>', $form['last_name']->label());
    $this->assertEquals('<label for="last_name"></label>', $form['last_name']->label(""));
  }
}*/
