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

class ValidatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \WTForms\Exceptions\NotImplemented
     * @expectedExceptionMessage Validator must have an overridden __construct method
     */
    public function testNotOverriddenConstructor()
    {
        new Validator;
    }

    /**
     * @expectedException \WTForms\Exceptions\NotImplemented
     * @expectedExceptionMessage Validator must have an overridden __invoke method
     */
    public function testNotOverriddenInvoke()
    {
        (new ValidatorOverride)->__invoke(new Form(), new DummyField());
    }
}
