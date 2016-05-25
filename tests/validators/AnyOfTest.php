<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/3/2016
 * Time: 9:08 PM
 */

namespace WTForms\Tests\validators;


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
   * @expectedException \WTForms\Validators\ValidationError
   * @expectedExceptionMessage test 7, 8, 9
   */
  public function testAnyOfValuesFormatter()
  {
    $any_of = new AnyOf("test %s", ["values" => [7, 8, 9]]);
    $any_of(new Form(), new DummyField(['data' => 4]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testAnyOfValueErrorExceptions1()
  {
    $any_of = new AnyOf("", ["values" => ['a', 'b', 'c']]);
    $this->assertNull($any_of(new Form(), new DummyField()));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testAnyOfValueErrorExceptions2()
  {
    $any_of = new AnyOf("", ["values" => [1, 2, 3]]);
    $this->assertNull($any_of(new Form(), new DummyField(['data' => 4])));
  }
}
