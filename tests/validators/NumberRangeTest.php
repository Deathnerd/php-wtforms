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

class NumberRangeTest extends \PHPUnit\Framework\TestCase
{
    public $form;

    public function setUp(): void
    {
        $this->form = new Form();
    }

    public function testNumberRange()
    {
        $number_range = new NumberRange("", ["min" => 5, "max" => 10]);
        $this->assertNull($number_range($this->form, new DummyField(["data" => 7])));
    }

    public function testOnlyMin()
    {
        $this->expectExceptionMessage("Number must be at least 5.");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        $only_min = new NumberRange("", ["min" => 5]);
        $this->assertNull($only_min($this->form, new DummyField(["data" => 500])));
        $only_min($this->form, new DummyField(["data" => 3]));
    }

    public function testOnlyMax()
    {
        $this->expectExceptionMessage("Number must be at most 50.");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        $only_max = new NumberRange("", ["max" => 50]);
        $this->assertNull($only_max($this->form, new DummyField(["data" => 4])));
        $only_max($this->form, new DummyField(["data" => 75]));
    }

    public function testNullInput()
    {
        $this->expectExceptionMessage("Number must be between 5 and 10.");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        (new NumberRange("", ["min" => 5, "max" => 10]))->__invoke($this->form, new DummyField(["data" => null]));
    }

    public function testZeroInput()
    {
        $this->expectExceptionMessage("Number must be between 5 and 10.");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        (new NumberRange("", ["min" => 5, "max" => 10]))->__invoke($this->form, new DummyField(["data" => 0]));
    }

    public function testAboveMaxInput()
    {
        $this->expectExceptionMessage("Number must be between 5 and 10.");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        (new NumberRange("", ["min" => 5, "max" => 10]))->__invoke($this->form, new DummyField(["data" => 12]));
    }

    public function testNegativeInput()
    {
        $this->expectExceptionMessage("Number must be between 5 and 10.");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        (new NumberRange("", ["min" => 5, "max" => 10]))->__invoke($this->form, new DummyField(["data" => -5]));
    }

    public function testCustomErrorMessage()
    {
        $this->expectExceptionMessage("foobar");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        (new NumberRange("foobar", ["min" => 5, "max" => 10]))->__invoke($this->form, new DummyField(["data" => -5]));
    }

    public function testCustomErrorMessageFormatting()
    {
        $this->expectExceptionMessage("5 foobar 10");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        (new NumberRange("%(min)d foobar %(max)d", ["min" => 5, "max" => 10]))->__invoke($this->form,
            new DummyField(["data" => -5]));
    }
}
