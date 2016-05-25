<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/24/2016
 * Time: 11:14 PM
 */

namespace WTForms\Tests\Validators;


use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\NoneOf;

class NoneOfTest extends \PHPUnit_Framework_TestCase
{
  public $form;

  public function setUp()
  {
    $this->form = new Form();
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testNoneOf()
  {
    $none_of = new NoneOf("", ["values" => ["a", "b", "c"]]);
    $this->assertNull($none_of($this->form, new DummyField(["data" => "d"])));
    $none_of($this->form, new DummyField(["data" => "a"]));
  }
}
