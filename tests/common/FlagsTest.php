<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/18/2016
 * Time: 10:28 PM.
 */

namespace WTForms\Tests\Common;

use WTForms\Fields\Core\StringField;
use WTForms\Flags;
use WTForms\Form;
use WTForms\Validators\DataRequired;

class FlagsTestForm extends Form
{
  /**
   * Form constructor.
   */
  public function __construct(array $options = [])
  {
    parent::__construct($options);
    $this->a = new StringField(['validators' => [new DataRequired()]]);
    $this->process($options);
  }
}

class FlagsTest extends \PHPUnit\Framework\TestCase
{
  public function setUp(): void
  {
  }

  public function testFlags()
  {
    $flags = new Flags();
    $flags->foo = true;
    $flags->_bar = false;
    $this->assertObjectHasProperty('foo', $flags);
    $this->assertObjectNotHasProperty('_foo', $flags);
    $this->assertTrue($flags->foo);
    $this->assertObjectHasProperty('_bar', $flags);
    $this->assertObjectNotHasProperty('bar', $flags);
    $this->assertFalse($flags->_bar);
    $this->assertFalse($flags->foo == $flags->_foo);
  }

  public function testExistingValues()
  {
    $form = new FlagsTestForm();
    $flags = $form->a->flags;
    $this->assertEquals(true, $flags->required);
    $this->assertTrue(property_exists($flags, 'required'));
    $this->assertEquals(false, $flags->optional);
    $this->assertTrue(property_exists($flags, 'optional'));
  }

  public function testAssignment()
  {
    $flags = (new FlagsTestForm())->a->flags;
    $this->assertFalse(property_exists($flags, 'optional'));
    $flags->optional = true;
    $this->assertEquals(true, $flags->optional);
    $this->assertTrue(property_exists($flags, 'optional'));
  }

  public function testUnset()
  {
    $flags = (new FlagsTestForm())->a->flags;
    unset($flags->required);
    $this->assertFalse(property_exists($flags, 'required'));
    $this->assertEquals(false, $flags->required);
  }
}
