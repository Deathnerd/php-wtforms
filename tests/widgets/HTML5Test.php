<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/24/2016
 * Time: 5:28 PM
 */

namespace WTForms\Tests\Widgets;

use WTForms\Widgets\HTML5\ColorInput;
use WTForms\Widgets\HTML5\DateInput;
use WTForms\Widgets\HTML5\DateTimeInput;
use WTForms\Widgets\HTML5\DateTimeLocalInput;
use WTForms\Widgets\HTML5\EmailInput;
use WTForms\Widgets\HTML5\MonthInput;
use WTForms\Widgets\HTML5\NumberInput;
use WTForms\Widgets\HTML5\RangeInput;
use WTForms\Widgets\HTML5\SearchInput;
use WTForms\Widgets\HTML5\TelInput;
use WTForms\Widgets\HTML5\TimeInput;
use WTForms\Widgets\HTML5\URLInput;
use WTForms\Widgets\HTML5\WeekInput;

class HTML5Test extends \PHPUnit_Framework_TestCase
{
  public $field;

  public function testNumber()
  {
    $i1 = new NumberInput(['step' => 'any']);
    $this->assertEquals('<input id="id" name="bar" step="any" type="number" value="42">', $i1($this->field));

    $i2 = new NumberInput(['step' => 2]);
    $this->assertEquals('<input id="id" name="bar" step="3" type="number" value="42">', $i2($this->field, ["step" => 3]));

    $i3 = new NumberInput(['min' => 10]);
    $this->assertEquals('<input id="id" min="10" name="bar" type="number" value="42">', $i3($this->field));
    $this->assertEquals('<input id="id" min="5" name="bar" type="number" value="42">', $i3($this->field, ["min" => 5]));

    $i4 = new NumberInput(["max" => 100]);
    $this->assertEquals('<input id="id" max="100" name="bar" type="number" value="42">', $i4($this->field));
    $this->assertEquals('<input id="id" max="50" name="bar" type="number" value="42">', $i4($this->field, ['max' => 50]));
  }

  public function testRange()
  {
    $i1 = new RangeInput(["step" => "any"]);
    $this->assertEquals('<input id="id" name="bar" step="any" type="range" value="42">', $i1($this->field));
    $i2 = new RangeInput(['step' => 2]);
    $this->assertEquals('<input id="id" name="bar" step="3" type="range" value="42">', $i2($this->field, ['step' => 3]));
  }

  public function testColor()
  {
    $color = new ColorInput();
    $this->field->data = "#ff0000";
    $this->assertEquals('<input id="id" name="bar" type="color" value="#ff0000">', $color($this->field));
  }

  public function testDate()
  {
    $date = new DateInput();
    $this->assertEquals('<input id="id" name="bar" type="date" value="42">', $date($this->field));
  }

  public function testDateTime()
  {
    $datetime = new DateTimeInput();
    $this->assertEquals('<input id="id" name="bar" type="datetime" value="42">', $datetime($this->field));
  }

  public function testDateTimeLocal()
  {
    $datetimelocal = new DateTimeLocalInput();
    $this->assertEquals('<input id="id" name="bar" type="datetime-local" value="42">', $datetimelocal($this->field));
  }

  public function testEmail()
  {
    $email = new EmailInput();
    $this->assertEquals('<input id="id" name="bar" type="email" value="42">', $email($this->field));
  }

  public function testMonth()
  {
    $month = new MonthInput();
    $this->assertEquals('<input id="id" name="bar" type="month" value="42">', $month($this->field));
  }

  public function testSearch()
  {
    $search = new SearchInput();
    $this->assertEquals('<input id="id" name="bar" type="search" value="42">', $search($this->field));
  }

  public function testTel()
  {
    $tel = new TelInput();
    $this->field->data = "+1(555) 555-555";
    $this->assertEquals('<input id="id" name="bar" type="tel" value="+1(555) 555-555">', $tel($this->field));
  }

  public function testTime()
  {
    $time = new TimeInput();
    $this->assertEquals('<input id="id" name="bar" type="time" value="42">', $time($this->field));
  }

  public function testURL()
  {
    $url = new URLInput();
    $this->field->data = "https://www.google.com";
    $this->assertEquals('<input id="id" name="bar" type="url" value="https://www.google.com">', $url($this->field));
  }

  public function testWeek()
  {
    $week = new WeekInput();
    $this->assertEquals('<input id="id" name="bar" type="week" value="42">', $week($this->field));
  }

  protected function setUp()
  {
    $this->field = new DummyField(['data' => '42', 'name' => 'bar', 'id' => 'id']);
  }

}
