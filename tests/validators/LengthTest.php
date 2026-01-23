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

class LengthTest extends \PHPUnit\Framework\TestCase
{
    public $form;
    public $field;

    protected function setUp(): void
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

    public function testInvalidMinOrMax()
    {
        $this->expectExceptionMessage("At least one of min or max must be specified");
        $this->expectException(\WTForms\Exceptions\TypeError::class);
        new Length("");
    }

    public function testMinMoreThanMax()
    {
        $this->expectExceptionMessage("min cannot be more than max");
        $this->expectException(\WTForms\Exceptions\ValueError::class);
        new Length("", ["min" => 5, "max" => 2]);
    }

    public function testInvalid_1()
    {
        $this->expectExceptionMessage("Field must be at least 7 characters long.");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        (new Length("", ["min" => 7]))->__invoke($this->form, $this->field);
    }

    public function testInvalid_2()
    {
        $this->expectExceptionMessage("Field cannot be longer than 5 characters long.");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        (new Length("", ["max" => 5]))->__invoke($this->form, $this->field);
    }

    public function testUserFormatting()
    {
        $this->expectExceptionMessage("2 and 5");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        (new Length("%(min)d and %(max)d", ["min" => 2, "max" => 5]))->__invoke($this->form, $this->field);
    }

    public function testDefaultMinMessage()
    {
        $this->expectExceptionMessage("Field must be at least 8 characters long.");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        (new Length("", ["min" => 8]))->__invoke($this->form, $this->field);
    }

    public function testDefaultMaxMessage()
    {
        $this->expectExceptionMessage("Field cannot be longer than 5 characters long.");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        (new Length("", ["max" => 5]))->__invoke($this->form, $this->field);
    }

    public function testDefaultBetweenMessage()
    {
        $this->expectExceptionMessage("Field must be between 2 and 5 characters long.");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        (new Length("", ["min" => 2, "max" => 5]))->__invoke($this->form, $this->field);
    }

    public function testSingularDefaultMinMessage()
    {
        $this->expectExceptionMessage("Field must be at least 1 character long.");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        $this->field->data = null;
        (new Length("", ["min" => 1]))->__invoke($this->form, $this->field);
    }

    public function testSingularDefaultMaxMessage()
    {
        $this->expectExceptionMessage("Field cannot be longer than 1 character long.");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        (new Length("", ["max" => 1]))->__invoke($this->form, $this->field);
    }
}
