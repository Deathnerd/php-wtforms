<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/3/2016
 * Time: 9:08 PM
 */

namespace WTForms\Tests\Validators;


use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\AnyOf;

class AnyOfTest extends \PHPUnit_Framework_TestCase
{

  public function testAnyOf()
  {
    $any_of = new AnyOf("", ['values' => ['a', 'b', 'c']]);
    $this->assertNull($any_of(new Form(), new DummyField(['data' => "b"])));
    $any_of = new AnyOf("", ['values' => [1, 2, 3]]);
    $this->assertNull($any_of(new Form(), new DummyField(['data' => 2])));
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   * @expectedExceptionMessage test 7, 8, 9
   */
  public function testAnyOfValuesFormatter()
  {
    (new AnyOf("test %s", ["values" => [7, 8, 9]]))->__invoke(new Form(), new DummyField(['data' => 4]));
  }

  /**
   * @expectedException WTForms\Exceptions\TypeError
   * @expectedExceptionMessage AnyOf validator expects an array of values!
   */
  public function testNoValues()
  {
    (new AnyOf("test %s"));
  }

  /**
   * @expectedException WTForms\Exceptions\TypeError
   * @expectedExceptionMessage Values passed to AnyOf must be in the form of an array
   */
  public function testInvalidValues()
  {
    (new AnyOf("test %s", ["values" => 5]));
  }

  /**
   * @expectedException WTForms\Exceptions\TypeError
   * @expectedExceptionMessage Formatter must be a callable; integer found
   */
  public function testInvalidFormatter()
  {
    (new AnyOf("test %s", ["values" => [1, 2, 3], "formatter" => 5]));
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   * @expectedExceptionMessage test 9::8::7
   */
  public function testAnyOfValuesFormatterOverride()
  {
    (new AnyOf("test %s", ["values" => [7, 8, 9], "formatter" => function ($values) {
      return implode("::", array_reverse($values));
    }]))->__invoke(new Form(), new DummyField(["data" => 4]));
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   * @expectedExceptionMessage Invalid value, must be one of: a, b, c
   */
  public function testAnyOfValueErrorExceptions1()
  {
    (new AnyOf("", ["values" => ['a', 'b', 'c']]))->__invoke(new Form(), new DummyField());
  }

  /**
   * @expectedException \WTForms\Exceptions\ValidationError
   * @expectedExceptionMessage Invalid value, must be one of: 1, 2, 3
   */
  public function testAnyOfValueErrorExceptions2()
  {
    (new AnyOf("", ["values" => [1, 2, 3]]))->__invoke(new Form(), new DummyField(['data' => 4]));
  }
}
