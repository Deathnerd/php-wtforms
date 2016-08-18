<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 8/18/2016
 * Time: 12:10 PM
 */

namespace WTForms\Tests\Validators;

use WTForms\Fields\Core\StringField;
use WTForms\Form;
use WTForms\Validators\RequiredIf;

class RequiredIfTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Form
     */
    protected $form;

    protected function setUp()
    {
        $this->form = new Form();
    }

    public function testValidationPass()
    {
        $this->form->a = new StringField();
        $this->form->b = new StringField([
            "validators" => [
                new RequiredIf("B is required if A is filled", ["fieldname" => "a"]),
            ],
        ]);
        $this->form->process(["a" => "foo", "b" => "bar"]);
        $this->assertTrue($this->form->validate());
        $this->assertEmpty($this->form->errors);
    }

    public function testNotRequiredIfOtherIsEmpty()
    {
        $this->form->a = new StringField();
        $this->form->b = new StringField([
            "validators" => [
                new RequiredIf("B is required if A is filled", ["fieldname" => "a"]),
            ],
        ]);
        $this->form->process(["b" => "bar"]);
        $this->assertTrue($this->form->validate());
        $this->assertEmpty($this->form->errors);
    }

    public function testValidatorFail()
    {
        $this->form->a = new StringField();
        $this->form->b = new StringField([
            "validators" => [
                new RequiredIf("B is required if A is filled", ["fieldname" => "a"]),
            ],
        ]);
        $this->form->process(["a" => "foo"]);
        $this->assertFalse($this->form->validate());
        $this->assertNotEmpty($this->form->errors);
    }

}
