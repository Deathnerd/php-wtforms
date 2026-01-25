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

class EmailTest extends \PHPUnit\Framework\TestCase
{
  public $form;

  protected function setUp(): void
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

  public function testNull()
  {
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new Email)->__invoke($this->form, new DummyField(["data" => null]));
  }

  public function testNullString()
  {
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new Email)->__invoke($this->form, new DummyField(["data" => ""]));
  }

  public function testWhitespaceString()
  {
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new Email)->__invoke($this->form, new DummyField(["data" => "  "]));
  }

  public function testFoo()
  {
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new Email)->__invoke($this->form, new DummyField(["data" => "foo"]));
  }

  public function testJustSuffix()
  {
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new Email)->__invoke($this->form, new DummyField(["data" => "bar.dk"]));
  }

  public function testFooAt()
  {
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new Email)->__invoke($this->form, new DummyField(["data" => "foo@"]));
  }

  public function testAtSuffix()
  {
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new Email)->__invoke($this->form, new DummyField(["data" => "@bar.dk"]));
  }

  public function testFooAtBar()
  {
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new Email)->__invoke($this->form, new DummyField(["data" => "foo@bar"]));
  }

  public function testFooAtDotBarDotAb()
  {
    $this->expectException(\WTForms\Exceptions\ValidationError::class);
    (new Email)->__invoke($this->form, new DummyField(["data" => "foo@.bar.ab"]));
  }
}
