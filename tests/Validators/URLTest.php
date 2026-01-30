<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/29/2016
 * Time: 5:34 PM.
 */

namespace WTForms\Tests\Validators;

use PHPUnit\Framework\TestCase;
use WTForms\Exceptions\ValidationError;
use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\URL;

class URLTest extends TestCase
{
  public $form;
  public $url;

  protected function setUp(): void
  {
    $this->form = new Form();
    $this->url = new URL();
  }

  public function testURL()
  {
    $this->assertNull($this->url->__invoke($this->form, new DummyField(['data' => 'http://foobar.dk'])));
    $this->assertNull($this->url->__invoke($this->form, new DummyField(['data' => 'http://foobar.dk/'])));
    $this->assertNull($this->url->__invoke($this->form, new DummyField(['data' => 'http://foobar.museum/foobar'])));
    $this->assertNull($this->url->__invoke($this->form, new DummyField(['data' => 'http://127.0.0.1/foobar'])));
    $this->assertNull($this->url->__invoke($this->form, new DummyField(['data' => 'http://127.0.0.1:9000/fake'])));
    $this->assertNull($this->url->__invoke($this->form, new DummyField(['data' => 'http://localhost/foobar'])));
    $this->assertNull($this->url->__invoke($this->form, new DummyField(['data' => 'http://foobar'])));
  }

  public function testNoProtocolAndCustomMessage()
  {
    $this->expectExceptionMessage('foo');
    $this->expectException(ValidationError::class);
    $this->url = new URL('foo');
    $this->url->__invoke($this->form, new DummyField(['data' => 'foobar.dk']));
  }
}
