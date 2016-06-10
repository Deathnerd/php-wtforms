<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 6/6/2016
 * Time: 5:19 PM
 */

namespace WTForms\Tests\Fields;


use Carbon\Carbon;
use WTForms\Fields\HTML5;
use WTForms\Form;

class HTML5FieldsTest extends \PHPUnit_Framework_TestCase
{
    public function testHTML5Fields()
    {
        $form = new Form();
        $form->search = new HTML5\SearchField();
        $form->telephone = new HTML5\TelField();
        $form->url = new HTML5\URLField();
        $form->email = new HTML5\EmailField();
        $form->datetime = new HTML5\DateTimeField();
        $form->date = new HTML5\DateField();
        $form->dt_local = new HTML5\DateTimeLocalField();
        $form->integer = new HTML5\IntegerField();
        $form->decimal = new HTML5\DecimalField();
        $form->int_range = new HTML5\IntegerRangeField();
        $form->decimal_range = new HTML5\DecimalRangeField();
        $form->process([
            "formdata" => [
                "search"        => "search",
                "telephone"     => "123456789",
                "url"           => "http://github.com/Deathnerd/php-wtforms",
                "email"         => "foo@bar.com",
                "datetime"      => '2013-09-05 00:23:42',
                "date"          => '2013-09-05',
                "dt_local"      => "2013-09-05 00:23:42",
                "integer"       => "42",
                "decimal"       => "43.5",
                "int_range"     => "4",
                "decimal_range" => "58"
            ]
        ]);

        $this->assertEquals('<input id="search" name="search" type="search" value="search">', "$form->search");
        $this->assertEquals('<input id="telephone" name="telephone" type="tel" value="123456789">', "$form->telephone");
        $this->assertEquals('<input id="url" name="url" type="url" value="http://github.com/Deathnerd/php-wtforms">',
            "$form->url");
        $this->assertEquals('<input id="email" name="email" type="email" value="foo@bar.com">', "$form->email");
        $this->assertEquals('<input id="datetime" name="datetime" type="datetime" value="2013-09-05 00:23:42">',
            "$form->datetime");
        $this->assertEquals(Carbon::create(2013, 9, 5, 0, 23, 42), $form->datetime->data);
        $this->assertEquals('<input id="date" name="date" type="date" value="2013-09-05">', "$form->date");
        $this->assertEquals(Carbon::create(2013, 9, 5, 0, 0, 0), $form->date->data);
        $this->assertEquals('<input id="dt_local" name="dt_local" type="datetime-local" value="2013-09-05 00:23:42">',
            "$form->dt_local");
        $this->assertEquals(Carbon::create(2013, 9, 5, 0, 23, 42), $form->dt_local->data);
        $this->assertEquals('<input id="integer" name="integer" step="1" type="number" value="42">', "$form->integer");
        $this->assertEquals(42, $form->integer->data);
        $this->assertEquals('<input id="decimal" name="decimal" step="1" type="number" value="43.5">',
            "$form->decimal");
        $this->assertEquals(43.5, $form->decimal->data);
        $this->assertEquals('<input id="int_range" name="int_range" step="1" type="range" value="4">',
            "$form->int_range");
        $this->assertEquals(4, $form->int_range->data);
        $this->assertEquals('<input id="decimal_range" name="decimal_range" step="any" type="range" value="58">',
            "$form->decimal_range");
        $this->assertEquals(58, $form->decimal_range->data);
    }
}
