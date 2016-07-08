<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/30/2016
 * Time: 7:18 PM
 */

namespace WTForms\Tests\Fields;


use Carbon\Carbon;
use WTForms\Fields\Core\DateField;
use WTForms\Fields\Core\DateTimeField;
use WTForms\Form;
use WTForms\Validators\Optional;

class DateFieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Form
     */
    public $form;

    public function testDateField()
    {
        $form = new Form();

        $form->dfield = new DateField(["name" => "dfield", "default" => new \DateTime('12/12/2012')]);
        $form->process([]);
        $this->assertEquals("%Y-%m-%d", $form->dfield->format);
        $this->assertTrue($form->dfield instanceof DateTimeField);
        $this->assertEquals('<input id="dfield" name="dfield" type="text" value="2012-12-12">', "$form->dfield");
    }

    public function testBasic()
    {
        $d = Carbon::create(2008, 5, 7, 0, 0, 0);
        $this->form->process(["formdata" => ["a" => ["2008-05-07"], "b" => ["05/07", "2008"]]]);
        $this->assertEquals($d, $this->form->a->data);
        $this->assertEquals("2008-05-07", $this->form->a->value);
        $this->assertEquals($d, $this->form->b->data);
        $this->assertEquals("05/07 2008", $this->form->b->value);
        $this->setUp();
        $this->form->process(["a" => Carbon::create(2014, 11, 12)]);
        $this->assertEquals("2014-11-12",
            $this->form->a->data->format(preg_replace('/%/', '', $this->form->a->format)));
        $this->assertEquals('<input id="a" name="a" type="text" value="2014-11-12">', "{$this->form->a}");
        $this->setUp();
        $this->form->process([]);
        $this->assertEquals('<input id="a" name="a" type="text" value="">', "{$this->form->a}");
    }

    public function testFailure()
    {
        $this->form->process(["formdata" => ["a" => ["2008-bb-cc"], "b" => ["hi"]]]);
        $this->assertFalse($this->form->validate());
        $this->assertEquals(1, count($this->form->a->process_errors));
        $this->assertEquals(1, count($this->form->a->errors));
        $this->assertEquals(1, count($this->form->b->errors));
        $this->assertEquals('Not a valid date value', $this->form->a->process_errors[0]);
    }

    public function testNoProcessErrorForOptionalFlag()
    {
        $form = new Form();
        $form->a = new DateField(["validators" => [new Optional()]]);
        $form->process(["formdata" => ["a" => [""]]]);
        $this->assertEmpty($form->a->process_errors);
    }

    protected function setUp()
    {
        $form = new Form();
        $form->a = new DateField();
        $form->b = new DateField(["format" => "m/d Y"]);
        $this->form = $form;
    }
}
