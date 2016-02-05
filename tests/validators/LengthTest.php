<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/4/2016
 * Time: 7:09 PM
 */

namespace Deathnerd\WTForms\Tests\validators;


use Deathnerd\WTForms\BaseForm;
use Deathnerd\WTForms\Tests\common\DummyField;
use Deathnerd\WTForms\Validators\Length;

class LengthTest extends \PHPUnit_Framework_TestCase
{
    public function testLength()
    {
        $field = new DummyField("foobar");
        $length = new Length(2, 6);
        $baseForm = new BaseForm([]);
        $this->assertNull($length($baseForm, $field));
        $length = new Length(2);
        $this->assertNull($length($baseForm, $field));
        $length = new Length(-1, 6);
        $this->assertNull($length($baseForm, $field));
    }
}
