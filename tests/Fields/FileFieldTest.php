<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 6/5/2016
 * Time: 4:05 PM.
 */

namespace WTForms\Tests\Fields;

use PHPUnit\Framework\TestCase;
use WTForms\Fields\Simple\FileField;
use WTForms\Form;

class FileFieldTest extends TestCase
{
  public function testFileField()
  {
    $form = new Form();
    $form->a = new FileField(['default' => 'LE DEFAULT']);
    $form->process([]);
    $this->assertEquals('<input id="a" name="a" type="file">', "$form->a");
  }
}
