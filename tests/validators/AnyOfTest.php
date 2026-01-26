<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/3/2016
 * Time: 9:08 PM.
 */

namespace WTForms\Tests\Validators;

use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\AnyOf;

class AnyOfTest extends \PHPUnit\Framework\TestCase
{
  public function testAnyOf()
  {
    $any_of = new AnyOf('', ['values' => ['a', 'b', 'c']]);
    $this->assertNull($any_of(new Form(), new DummyField(['data' => 'b'])));
    $any_of = new AnyOf('', ['values' => [1, 2, 3]]);
    $this->assertNull($any_of(new Form(), new DummyField(['data' => 2])));
  }

  public function testAnyOfValuesFormatter()
  {
    $this->expectExceptionMessage('test 7, 8, 9');
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new AnyOf('test %s', ['values' => [7, 8, 9]]))->__invoke(new Form(), new DummyField(['data' => 4]));
  }

  public function testNoValues()
  {
    $this->expectExceptionMessage('AnyOf validator expects an array of values!');
    $this->expectException(\WTForms\Exceptions\TypeError::class);
    new AnyOf('test %s');
  }

  public function testInvalidValues()
  {
    $this->expectExceptionMessage('Values passed to AnyOf must be in the form of an array');
    $this->expectException(\WTForms\Exceptions\TypeError::class);
    new AnyOf('test %s', ['values' => 5]);
  }

  public function testInvalidFormatter()
  {
    $this->expectExceptionMessage('Formatter must be a callable; integer found');
    $this->expectException(\WTForms\Exceptions\TypeError::class);
    new AnyOf('test %s', ['values' => [1, 2, 3], 'formatter' => 5]);
  }

  public function testAnyOfValuesFormatterOverride()
  {
    $this->expectExceptionMessage('test 9::8::7');
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new AnyOf('test %s', [
      'values'    => [7, 8, 9],
      'formatter' => function ($values) {
        return implode('::', array_reverse($values));
      },
    ]))->__invoke(new Form(), new DummyField(['data' => 4]));
  }

  public function testAnyOfValueErrorExceptions1()
  {
    $this->expectExceptionMessage('Invalid value, must be one of: a, b, c');
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new AnyOf('', ['values' => ['a', 'b', 'c']]))->__invoke(new Form(), new DummyField());
  }

  public function testAnyOfValueErrorExceptions2()
  {
    $this->expectExceptionMessage('Invalid value, must be one of: 1, 2, 3');
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new AnyOf('', ['values' => [1, 2, 3]]))->__invoke(new Form(), new DummyField(['data' => 4]));
  }
}
