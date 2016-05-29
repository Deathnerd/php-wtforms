<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/29/2016
 * Time: 4:34 PM
 */

namespace WTForms\Tests\Validators;


use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\NumberRange;

class NumberRangeTest extends \PHPUnit_Framework_TestCase
{
  public $form;

  public function setUp()
  {
    $this->form = new Form();
  }

  public function testNumberRange()
  {
    $number_range = new NumberRange("", ["min" => 5, "max" => 10]);
    $this->assertNull($number_range($this->form, new DummyField(["data" => 7])));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   * @expectedExceptionMessage Number must be at least 5.
   */
  public function testOnlyMin()
  {
    $only_min = new NumberRange("", ["min" => 5]);
    $this->assertNull($only_min($this->form, new DummyField(["data" => 500])));
    $only_min($this->form, new DummyField(["data" => 3]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   * @expectedExceptionMessage Number must be at most 50.
   */
  public function testOnlyMax()
  {
    $only_max = new NumberRange("", ["max" => 50]);
    $this->assertNull($only_max($this->form, new DummyField(["data" => 4])));
    $only_max($this->form, new DummyField(["data" => 75]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   * @expectedExceptionMessage Number must be between 5 and 10.
   */
  public function testNullInput()
  {
    (new NumberRange("", ["min" => 5, "max" => 10]))->__invoke($this->form, new DummyField(["data" => null]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   * @expectedExceptionMessage Number must be between 5 and 10.
   */
  public function testZeroInput()
  {
    (new NumberRange("", ["min" => 5, "max" => 10]))->__invoke($this->form, new DummyField(["data" => 0]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   * @expectedExceptionMessage Number must be between 5 and 10.
   */
  public function testAboveMaxInput()
  {
    (new NumberRange("", ["min" => 5, "max" => 10]))->__invoke($this->form, new DummyField(["data" => 12]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   * @expectedExceptionMessage Number must be between 5 and 10.
   */
  public function testNegativeInput()
  {
    (new NumberRange("", ["min" => 5, "max" => 10]))->__invoke($this->form, new DummyField(["data" => -5]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   * @expectedExceptionMessage foobar
   */
  public function testCustomErrorMessage()
  {
    (new NumberRange("foobar", ["min" => 5, "max" => 10]))->__invoke($this->form, new DummyField(["data" => -5]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   * @expectedExceptionMessage 5 foobar 10
   */
  public function testCustomErrorMessageFormatting()
  {
    (new NumberRange("%(min)d foobar %(max)d", ["min" => 5, "max" => 10]))->__invoke($this->form, new DummyField(["data" => -5]));
  }
}
