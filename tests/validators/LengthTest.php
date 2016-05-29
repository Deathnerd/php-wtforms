<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/4/2016
 * Time: 7:09 PM
 */

namespace WTForms\Tests\Validators;


use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\Length;

class LengthTest extends \PHPUnit_Framework_TestCase
{
  public $form;
  public $field;

  protected function setUp()
  {
    $this->form = new Form();
    $this->field = new DummyField(["data" => "foobar"]);
  }


  public function testLength()
  {
    $length = new Length("", ['min' => 2, 'max' => 6]);
    $this->assertNull($length($this->form, $this->field));
    $length = new Length("", ['min' => 2]);
    $this->assertNull($length($this->form, $this->field));
    $length = new Length("", ['min' => 2, 'max' => 6]);
    $this->assertNull($length($this->form, $this->field));
  }

  /**
   * @expectedException \WTForms\Exceptions\TypeError
   * @expectedExceptionMessage At least one of min or max must be specified
   */
  public function testInvalidMinOrMax()
  {
    new Length("");
  }

  /**
   * @expectedException \WTForms\Exceptions\ValueError
   * @expectedExceptionMessage min cannot be more than max
   */
  public function testMinMoreThanMax()
  {
    new Length("", ["min" => 5, "max" => 2]);
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   * @expectedExceptionMessage Field must be at least 7 characters long.
   */
  public function testInvalid_1()
  {
    (new Length("", ["min" => 7]))->__invoke($this->form, $this->field);
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   * @expectedExceptionMessage Field cannot be longer than 5 characters long.
   */
  public function testInvalid_2()
  {
    (new Length("", ["max" => 5]))->__invoke($this->form, $this->field);
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   * @expectedExceptionMessage 2 and 5
   */
  public function testUserFormatting()
  {
    (new Length("%(min)d and %(max)d", ["min" => 2, "max" => 5]))->__invoke($this->form, $this->field);
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   * @expectedExceptionMessage Field must be at least 8 characters long.
   */
  public function testDefaultMinMessage()
  {
    (new Length("", ["min" => 8]))->__invoke($this->form, $this->field);
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   * @expectedExceptionMessage Field cannot be longer than 5 characters long.
   */
  public function testDefaultMaxMessage()
  {
    (new Length("", ["max" => 5]))->__invoke($this->form, $this->field);
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   * @expectedExceptionMessage Field must be between 2 and 5 characters long.
   */
  public function testDefaultBetweenMessage()
  {
    (new Length("", ["min" => 2, "max" => 5]))->__invoke($this->form, $this->field);
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   * @expectedExceptionMessage Field must be at least 1 character long.
   */
  public function testSingularDefaultMinMessage()
  {
    $this->field->data = null;
    (new Length("", ["min" => 1]))->__invoke($this->form, $this->field);
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   * @expectedExceptionMessage Field cannot be longer than 1 character long.
   */
  public function testSingularDefaultMaxMessage()
  {
    (new Length("", ["max" => 1]))->__invoke($this->form, $this->field);
  }
}
