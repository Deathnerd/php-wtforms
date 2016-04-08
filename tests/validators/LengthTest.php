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

class LengthTest extends \PHPUnit_Framework_TestCase
{
  public function testLength()
  {
    $field = new DummyField("", ["data" => "foobar"]);
    $length = new Length("", ['min' => 2, 'max' => 6]);
    $baseForm = new Form();
    $this->assertNull($length($baseForm, $field));
    $length = new Length("", ['min' => 2]);
    $this->assertNull($length($baseForm, $field));
    $length = new Length("", ['min' => 2, 'max' => 6]);
    $this->assertNull($length($baseForm, $field));
  }
}
