<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 6/5/2016
 * Time: 4:11 PM
 */

namespace WTForms\Tests\Fields;

use WTForms\Fields\Simple\PasswordField;
use WTForms\Form;
use WTForms\Widgets\Core\PasswordInput;

class PasswordFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testPasswordField()
    {
        $form = new Form();
        $form->a = new PasswordField(["widget" => new PasswordInput(false), "default" => "LE DEFAULT"]);
        $form->b = new PasswordField(["default" => "Hai"]);
        $form->process([]);
        $this->assertEquals('<input id="a" name="a" type="password" value="LE DEFAULT">', "$form->a");
        $this->assertEquals('<input id="b" name="b" type="password" value="">', "$form->b");
    }
}
