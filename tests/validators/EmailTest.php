<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/24/2016
 * Time: 9:41 PM
 */

namespace WTForms\Tests\Validators;


use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\Email;

class EmailTest extends \PHPUnit_Framework_TestCase
{
  public $form;

  protected function setUp()
  {
    $this->form = new Form();
  }

  public function testEmail()
  {
    $email = new Email();
    $this->assertNull($email($this->form, new DummyField(["data" => 'foo@bar.dk'])));
    $this->assertNull($email($this->form, new DummyField(["data" => "123@bar.dk"])));
    $this->assertNull($email($this->form, new DummyField(["data" => "foo@456.dk"])));
    $this->assertNull($email($this->form, new DummyField(['data' => 'foo@bar456.info'])));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testNull()
  {
    (new Email)->__invoke($this->form, new DummyField(["data" => null]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testNullString()
  {
    (new Email)->__invoke($this->form, new DummyField(["data" => ""]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testWhitespaceString()
  {
    (new Email)->__invoke($this->form, new DummyField(["data" => "  "]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testFoo()
  {
    (new Email)->__invoke($this->form, new DummyField(["data" => "foo"]));
  }

  /**
   * @expectedException  \WTForms\Validators\ValidationError
   */
  public function testJustSuffix()
  {
    (new Email)->__invoke($this->form, new DummyField(["data" => "bar.dk"]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testFooAt()
  {
    (new Email)->__invoke($this->form, new DummyField(["data" => "foo@"]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testAtSuffix()
  {
    (new Email)->__invoke($this->form, new DummyField(["data" => "@bar.dk"]));
  }

  /**
   * @expectedException  \WTForms\Validators\ValidationError
   */
  public function testFooAtBar()
  {
    (new Email)->__invoke($this->form, new DummyField(["data" => "foo@bar"]));
  }

  /**
   * @expectedException \WTForms\Validators\ValidationError
   */
  public function testFooAtDotBarDotAb()
  {
    (new Email)->__invoke($this->form, new DummyField(["data" => "foo@.bar.ab"]));
  }
}
