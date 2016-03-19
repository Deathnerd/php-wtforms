<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/4/2016
 * Time: 7:09 PM
 */

namespace WTForms\Tests\validators;


use WTForms\Form;
use WTForms\Tests\Common\DummyField;
use WTForms\Validators\Length;

class LengthTest extends \PHPUnit_Framework_TestCase
{
    public function testLength()
    {
        $field = new DummyField("foobar");
        $length = new Length(2, 6);
        $baseForm = new Form([]);
        $this->assertNull($length($baseForm, $field));
        $length = new Length(2);
        $this->assertNull($length($baseForm, $field));
        $length = new Length(-1, 6);
        $this->assertNull($length($baseForm, $field));
    }
}
