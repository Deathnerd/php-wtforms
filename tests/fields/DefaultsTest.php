<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 4/8/2016
 * Time: 12:53 PM
 */

namespace WTForms\Tests\Fields;


use WTForms\DefaultMeta;
use WTForms\Fields\Core\StringField;
use WTForms\Form;
use WTForms\Widgets\Core\TextInput;


/*class DefaultsTest extends \PHPUnit_Framework_TestCase
{
  public function testDefaults()
  {
    $expected = 42;
    $form = new Form(['test' => new StringField(['default' => $expected, 'widget' => new TextInput()])], '', new DefaultMeta());
    $form['test']->process(null);
    $this->assertEquals($expected, $form['test']->data);

//      TODO: Test callable default
  }
}*/
