<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/15/2016
 * Time: 9:11 PM
 */

namespace WTForms\Tests\Common;
require_once('../../vendor/autoload.php');

use Composer\Autoload\ClassLoader;
use WTForms\Fields\Core\Field;
use WTForms\Fields\Core\StringField;
use WTForms\Forms;
use mindplay\annotations\AnnotationCache;
use mindplay\annotations\Annotations;
use WTForms\Tests\SupportingClasses\Helper;
use WTForms\Tests\SupportingClasses\AnnotatedHelper;

class FormsTest extends \PHPUnit_Framework_TestCase
{
  protected $helper;
  protected $annotated_helper;

  public function setUp()
  {
    Annotations::$config['cache'] = new AnnotationCache(__DIR__ . "/../runtime");
    $annotationManager = Annotations::getManager();
    $annotationManager->registry['stringField'] = 'WTForms\Fields\Core\Annotations\StringFieldAnnotation';
    $annotationManager->registry['form'] = 'WTForms\FormAnnotation';
    $annotationManager->registry['inputRequired'] = 'WTForms\Validators\Annotations\InputRequiredAnnotation';
    $this->helper = new Helper;
    $this->annotated_helper = new AnnotatedHelper;
  }

  /**
   * Should return an empty form object
   */
  public function testCreateNonAnnotated()
  {
    $form = Forms::create($this->helper);
    $this->assertFalse(property_exists($form, 'foo') || property_exists($form, 'bar'));
  }

  /**
   * Test for form annotation overrides
   */
  public function testCreateFormAnnotation()
  {
    $form = Forms::create($this->annotated_helper);
    $this->assertEquals('foo', $form->prefix);
    $this->assertAttributeInstanceOf('WTForms\Tests\SupportingClasses\FooMeta', 'meta', $form);
    $this->assertEquals(false, $form->csrf);
  }

  /**
   * Test for field annotations
   */
  public function testCreateFieldAnnotation(){
    $form = Forms::create($this->annotated_helper);
    $this->assertNotEmpty($form->fields);
    $this->assertTrue($form['first_name'] instanceof Field && $form['first_name'] instanceof StringField);
    $this->assertEquals('<label for="fname">First Name</label>', $form['first_name']->label->__invoke("First Name"));
    echo $form['first_name']->label;
    echo $form['first_name'];
  }
  // test for populated by array
  // test for populated by object
  // test for populated by array and object
}
