<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/19/2016
 * Time: 9:26 AM
 */

namespace WTForms\Tests\Fields;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;
use WTForms\Annotations\Fields\Core\FormField;
use WTForms\Annotations\Fields\Core\StringField;
use WTForms\Annotations\Validators\DataRequired;
use WTForms\Forms;
use WTForms\Annotations\Form;

/**
 * @Form
 */
class F
{
  /**
   * @var string
   * @StringField(validators={@DataRequired})
   */
  public $a;

  /**
   * @var string
   * @StringField
   */
  public $b;
}

/**
 * @Form
 */
class F1
{
  /**
   * @var string
   * @FormField(form_class="WTForms\Tests\Fields\F")
   */
  public $a;
}

/**
 * @Form
 */
class F2
{
  /**
   * @var string
   * @FormField(form_class="WTForms\Tests\Fields\F", separator="::")
   */
  public $a;
}

/*class FormFieldTest extends \PHPUnit_Framework_TestCase
{
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
    Forms::init($this->reader, $this->registry);
  }

  public function testFormData()
  {
    $form = Forms::create(new F1, ["a-a" => ["moo"]]);
    $this->assertEquals("a-a", $form->a->form->a->name);
    $this->assertEquals("moo", $form->a['a']->data);
    $this->assertEquals("", $form->a['b']->data);
    $this->assertTrue($form->validate());
  }
}*/
