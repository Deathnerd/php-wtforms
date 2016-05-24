<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/24/2016
 * Time: 12:15 PM
 */

namespace WTForms\Tests\Common;

use WTForms\DefaultMeta;
use WTForms\Fields\Core\StringField;
use WTForms\Form;

class FMeta extends DefaultMeta
{
  public $foo = 9;
}

class F extends Form
{
  public function __construct(array $options = [])
  {
    parent::__construct($options);
    $this->meta = new FMeta();
    $this->test = new StringField(["name" => "test"]);
    $this->process($options);
  }
}

class GMeta extends DefaultMeta
{
  public $foo = 12;
  public $bar = 8;
}

class G extends Form
{
  public function __construct(array $options = [])
  {
    parent::__construct($options);
    $this->meta = new GMeta();
    $this->test = new StringField(["name" => "test"]);
    $this->process($options);
  }
}


class DefaultMetaTest extends \PHPUnit_Framework_TestCase
{
  public function testBasic()
  {
    $form = new G();
    $meta = $form->meta;
    $this->assertEquals(12, $meta->foo);
    $this->assertEquals(8, $meta->bar);
    $this->assertFalse($meta->csrf);
  }
}
