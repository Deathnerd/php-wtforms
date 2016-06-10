<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/27/2016
 * Time: 4:14 PM
 */

namespace WTForms\Tests\Fields;

use WTForms\Fields\Core\BooleanField;
use WTForms\Form;

class BooleanFieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Form
     */
    public $form;

    public function setUp()
    {
        $form = new Form();
        $form->bool1 = new BooleanField();
        $form->bool2 = new BooleanField(["default" => true, "false_values" => []]);
        $form->process([]);
        $this->form = $form;
    }

    public function testDefaults()
    {
        $this->assertEquals([], $this->form->bool1->raw_data);
        $this->assertEquals(false, $this->form->bool1->data);
        $this->assertEquals(true, $this->form->bool2->data);
    }

    public function testRendering()
    {
        $this->form->process(["formdata" => ["bool2" => ["x"]]]);
        $this->assertEquals('<input id="bool1" name="bool1" type="checkbox" value="y">',
            $this->form->bool1->__invoke());
        $this->assertEquals('<input checked id="bool2" name="bool2" type="checkbox" value="x">',
            $this->form->bool2->__invoke());
        $this->assertEquals(["x"], $this->form->bool2->raw_data);
    }

    public function testWithPostData()
    {
        $this->form->process(["formdata" => ["bool1" => ["a"]]]);
        $this->assertEquals(['a'], $this->form->bool1->raw_data);
        $this->assertEquals(true, $this->form->bool1->data);

        $this->form->process(["formdata" => ["bool1" => ["false"], "bool2" => ["false"]]]);
        $this->assertEquals(false, $this->form->bool1->data);
        $this->assertEquals(true, $this->form->bool2->data);
    }

    public function testWithObjectData()
    {
        $this->assertEquals(false, $this->form->bool1->data);
        $this->assertEquals(true, empty($this->form->bool1->raw_data));
        $this->assertEquals(true, $this->form->bool2->data);
    }

    public function testWithObjectAndPostData()
    {
        $obj = new \stdClass();
        $obj->bool1 = null;
        $obj->bool2 = true;
        $this->form->process(["formdata" => ["bool1" => ["y"]], "obj" => $obj]);
        $this->assertEquals(true, $this->form->bool1->data);
        $this->assertEquals(false, $this->form->bool2->data);
    }
}
