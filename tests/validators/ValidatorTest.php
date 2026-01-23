<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/29/2016
 * Time: 5:48 PM
 */

namespace WTForms\Tests\Validators;


use WTForms\Form;
use WTForms\Tests\SupportingClasses\DummyField;
use WTForms\Validators\Validator;

class ValidatorOverride extends Validator
{
    public function __construct($message = "", array $options = [])
    {
    }

}

class ValidatorTest extends \PHPUnit\Framework\TestCase
{

    public function testNotOverriddenConstructor()
    {
        $this->expectExceptionMessage("Validator must have an overridden __construct method");
        $this->expectException(\WTForms\Exceptions\NotImplemented::class);
        new Validator;
    }

    public function testNotOverriddenInvoke()
    {
        $this->expectExceptionMessage("Validator must have an overridden __invoke method");
        $this->expectException(\WTForms\Exceptions\NotImplemented::class);
        (new ValidatorOverride)->__invoke(new Form(), new DummyField());
    }
}
