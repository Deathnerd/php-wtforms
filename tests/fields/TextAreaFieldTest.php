<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 6/5/2016
 * Time: 4:43 PM
 */

namespace WTForms\Tests\Fields;


use WTForms\Fields\Simple\TextAreaField;
use WTForms\Form;

class TextAreaFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testTextAreaField()
    {
        $form = new Form();
        $form->a = new TextAreaField(["default" => "LE DEFAULT"]);
        $form->process([]);
        $this->assertEquals('<textarea id="a" name="a">LE DEFAULT</textarea>', "$form->a");
    }
}
