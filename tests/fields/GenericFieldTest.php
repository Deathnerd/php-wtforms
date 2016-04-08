<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 4/8/2016
 * Time: 3:43 PM
 */

namespace WTForms\Tests\Fields;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;
use WTForms\Fields\Core\StringField;
use WTForms\Form;
use WTForms\Forms;
use WTForms\Tests\SupportingClasses\AnnotatedHelper;
use WTForms\Tests\SupportingClasses\Helper;

class GenericFieldTest extends \PHPUnit_Framework_TestCase
{
  protected $helper;
  protected $annotated_helper;
  protected $registry;
  protected $reader;
  /**
   * @var Form
   */
  protected $form;
  /**
   * @var StringField
   */
  protected $field;

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
    $this->field = $this->form->a;
  }

  public function testProcessData()
  {
    $this->field->processFormData([42]);
    $this->assertEquals(42, $this->field->data);
  }

  /**
   * Test whether meta is overridden from parent form's meta
   */
  public function testMetaAttribute()
  {
    $this->assertEquals($this->form->a->meta, $this->field->meta);
  }

  public function testRenderKw()
  {
    $this->assertEquals('<input id="a" type="text" value="hello" readonly foo="bar" name="a">', $this->form->a->__invoke());
    $this->assertEquals('<input id="a" type="text" value="hello" readonly foo="baz" name="a">', $this->form->a->__invoke(['foo' => 'baz']));
    $this->assertEquals('<input id="a" type="text" value="hello" foo="baz" other="hello" name="a">', $this->form->a->__invoke(['foo' => 'baz', 'readonly' => false, 'other' => 'hello']));
  }
}
