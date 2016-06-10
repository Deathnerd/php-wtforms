<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 5/30/2016
 * Time: 11:23 AM
 */

namespace WTForms\Tests\Fields;


use WTForms\Exceptions\ValueError;
use WTForms\Fields\Core\StringField;
use WTForms\Form;

class FiltersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Form
     */
    public $form;

    protected function setUp()
    {
        $this->form = new Form();
        $this->form->a = new StringField([
            "default" => " hello",
            "filters" => [
                function ($x) {
                    return trim($x);
                }
            ]
        ]);
        $this->form->b = new StringField([
            "default" => "42",
            "filters" => [
                function ($x) {
                    if (!is_numeric($x)) {
                        throw new ValueError("Tried to cast $x to an integer");
                    }

                    return intval($x);
                },
                function ($x) {
                    return -$x;
                }
            ]
        ]);
        $this->form->process([]);
    }

    public function testWorking()
    {
        $this->assertEquals("hello", $this->form->a->data);
        $this->assertEquals(-42, $this->form->b->data);
        $this->assertTrue($this->form->validate());
    }

    public function testFailure()
    {
        $this->form->process(["a" => "  foo bar  ", "b" => "hi"]);
        $this->assertEquals('foo bar', $this->form->a->data);
        $this->assertEquals('hi', $this->form->b->data);
        $this->assertEquals(1, count($this->form->b->process_errors));
        $this->assertFalse($this->form->validate());
    }

}
