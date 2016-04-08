<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/18/2016
 * Time: 10:28 PM
 */

namespace WTForms\Tests\Common;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;
use WTForms\Flags;
use WTForms\Forms;
use WTForms\Tests\SupportingClasses\AnnotatedHelper;

class FlagsTest extends \PHPUnit_Framework_TestCase
{
  protected $annotated_helper;
  protected $registry;
  protected $reader;
  protected $flags;
  protected $form;

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
    $this->form = Forms::create($this->annotated_helper);
    $this->flags = $this->form['first_name']->flags;
  }

  public function testFlags()
  {
    $flags = new Flags();
    $flags->foo = true;
    $flags->_bar = false;
    $this->assertObjectHasAttribute("foo", $flags);
    $this->assertObjectNotHasAttribute("_foo", $flags);
    $this->assertTrue($flags->foo);
    $this->assertObjectHasAttribute("_bar", $flags);
    $this->assertObjectNotHasAttribute("bar", $flags);
    $this->assertFalse($flags->_bar);
    $this->assertFalse($flags->foo == $flags->_foo);
  }

  public function testExistingValues()
  {
    $this->assertEquals(true, $this->flags->required);
    $this->assertTrue(property_exists($this->flags, 'required'));
    $this->assertEquals(false, $this->flags->optional);
    $this->assertTrue(property_exists($this->flags, 'optional'));
  }

  public function testAssignment()
  {
    $this->assertFalse(property_exists($this->flags, 'optional'));
    $this->flags->optional = true;
    $this->assertEquals(true, $this->flags->optional);
    $this->assertTrue(property_exists($this->flags, 'optional'));
  }

  public function testUnset()
  {
    unset($this->flags->required);
    $this->assertFalse(property_exists($this->flags, 'required'));
    $this->assertEquals(false, $this->flags->required);
  }

}
