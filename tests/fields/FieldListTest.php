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
use WTForms\Form;
use WTForms\Forms;
use WTForms\Tests\SupportingClasses\AnnotatedHelper;
use WTForms\Tests\SupportingClasses\Helper;

class FieldListTest extends \PHPUnit_Framework_TestCase
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
  }

  public function testForm()
  {
    $data = ['foo', 'hi', 'rawr'];
    $this->form = Forms::create($this->annotated_helper, [], ["list_of_fields" => $data]);
    $list_of_fields = $this->form->list_of_fields;
    $this->assertEquals("hi", $list_of_fields->entries[1]->data);
    $this->assertEquals("list_of_fields-1", $list_of_fields->entries[1]->name);
    $this->assertEquals(count($data), count(array_intersect($list_of_fields->data, $data)));
    $this->assertEquals(3, count($list_of_fields->entries));

    $post_data = ["list_of_fields-0" => ["bleh"],
                  "list_of_fields-3" => ["yarg"],
                  "list_of_fields-4" => [""],
                  "list_of_fields-7" => ["mmm"]];
    $this->form = Forms::create($this->annotated_helper, $post_data);
    $this->assertEquals(4, count($this->form->list_of_fields->entries));
    $this->assertEquals(["bleh", "yarg", "", "mmm"], $this->form->list_of_fields->data);
    
  }
}
