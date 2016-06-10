<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/24/2016
 * Time: 5:28 PM
 */

namespace WTForms\Tests\Widgets;

use WTForms\Widgets\HTML5;

class HTML5Test extends \PHPUnit_Framework_TestCase
{
    public $field;

    public function testNumber()
    {
        $i1 = new HTML5\NumberInput(['step' => 'any']);
        $this->assertEquals('<input id="id" name="bar" step="any" type="number" value="42">', $i1($this->field));

        $i2 = new HTML5\NumberInput(['step' => 2]);
        $this->assertEquals('<input id="id" name="bar" step="3" type="number" value="42">',
            $i2($this->field, ["step" => 3]));

        $i3 = new HTML5\NumberInput(['min' => 10]);
        $this->assertEquals('<input id="id" min="10" name="bar" type="number" value="42">', $i3($this->field));
        $this->assertEquals('<input id="id" min="5" name="bar" type="number" value="42">',
            $i3($this->field, ["min" => 5]));

        $i4 = new HTML5\NumberInput(["max" => 100]);
        $this->assertEquals('<input id="id" max="100" name="bar" type="number" value="42">', $i4($this->field));
        $this->assertEquals('<input id="id" max="50" name="bar" type="number" value="42">',
            $i4($this->field, ['max' => 50]));
    }

    public function testRange()
    {
        $i1 = new HTML5\RangeInput(["step" => "any"]);
        $this->assertEquals('<input id="id" name="bar" step="any" type="range" value="42">', $i1($this->field));
        $i2 = new HTML5\RangeInput(['step' => 2]);
        $this->assertEquals('<input id="id" name="bar" step="3" type="range" value="42">',
            $i2($this->field, ['step' => 3]));
    }

    public function testColor()
    {
        $color = new HTML5\ColorInput();
        $this->field->data = "#ff0000";
        $this->assertEquals('<input id="id" name="bar" type="color" value="#ff0000">', $color($this->field));
    }

    public function testDate()
    {
        $date = new HTML5\DateInput();
        $this->assertEquals('<input id="id" name="bar" type="date" value="42">', $date($this->field));
    }

    public function testDateTime()
    {
        $datetime = new HTML5\DateTimeInput();
        $this->assertEquals('<input id="id" name="bar" type="datetime" value="42">', $datetime($this->field));
    }

    public function testDateTimeLocal()
    {
        $datetimelocal = new HTML5\DateTimeLocalInput();
        $this->assertEquals('<input id="id" name="bar" type="datetime-local" value="42">',
            $datetimelocal($this->field));
    }

    public function testEmail()
    {
        $email = new HTML5\EmailInput();
        $this->assertEquals('<input id="id" name="bar" type="email" value="42">', $email($this->field));
    }

    public function testMonth()
    {
        $month = new HTML5\MonthInput();
        $this->assertEquals('<input id="id" name="bar" type="month" value="42">', $month($this->field));
    }

    public function testSearch()
    {
        $search = new HTML5\SearchInput();
        $this->assertEquals('<input id="id" name="bar" type="search" value="42">', $search($this->field));
    }

    public function testTel()
    {
        $tel = new HTML5\TelInput();
        $this->field->data = "+1(555) 555-555";
        $this->assertEquals('<input id="id" name="bar" type="tel" value="+1(555) 555-555">', $tel($this->field));
    }

    public function testTime()
    {
        $time = new HTML5\TimeInput();
        $this->assertEquals('<input id="id" name="bar" type="time" value="42">', $time($this->field));
    }

    public function testURL()
    {
        $url = new HTML5\URLInput();
        $this->field->data = "https://www.google.com";
        $this->assertEquals('<input id="id" name="bar" type="url" value="https://www.google.com">', $url($this->field));
    }

    public function testWeek()
    {
        $week = new HTML5\WeekInput();
        $this->assertEquals('<input id="id" name="bar" type="week" value="42">', $week($this->field));
    }

    protected function setUp()
    {
        $this->field = new \WTForms\Tests\SupportingClasses\DummyField(['data' => '42', 'name' => 'bar', 'id' => 'id']);
    }

}
