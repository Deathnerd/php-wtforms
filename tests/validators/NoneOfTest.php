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

class NoneOfTest extends \PHPUnit\Framework\TestCase
{
    public $form;

    public function setUp(): void
    {
        $this->form = new Form();
    }

    public function testNoneOf()
    {
        $this->expectExceptionMessage("Invalid value, can't be any of: a, b, c.");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        $none_of = new NoneOf("", ["values" => ["a", "b", "c"]]);
        $this->assertNull($none_of($this->form, new DummyField(["data" => "d"])));
        $none_of($this->form, new DummyField(["data" => "a"]));
    }

    public function testCustomErrorMessage()
    {
        $this->expectExceptionMessage("Y'all can't have these in your input: a, b, c.");
        $this->expectException(\WTForms\Exceptions\ValidationError::class);
        (new NoneOf("Y'all can't have these in your input: %s.", ["values" => ["a", "b", "c"]]))->__invoke($this->form,
            new DummyField(["data" => "a"]));
    }
}
