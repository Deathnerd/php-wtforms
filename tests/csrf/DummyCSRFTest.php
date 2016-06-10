<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 6/6/2016
 * Time: 11:04 PM
 */

namespace WTForms\Tests\CSRF;

use WTForms\CSRF\Core\CSRF;
use WTForms\DefaultMeta;
use WTForms\Fields\Core\StringField;
use WTForms\Form;

class FMeta extends DefaultMeta
{
    public $csrf_class = '\WTForms\Tests\SupportingClasses\DummyCSRF';
    public $csrf = true;
}

class F extends Form
{
    public function __construct(array $options = [])
    {
        $this->meta = new FMeta();
        parent::__construct($options);
        $this->a = new StringField();
        $this->process($options);
    }

}

class DummyCSRFTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \WTForms\Exceptions\NotImplemented
     */
    public function testBaseClass()
    {
        (new F(["meta" => ["csrf_class" => new CSRF]]));
    }

    public function testBasicImpl()
    {
        $form = new F();
        $field_names = [];
        foreach ($form as $name => $field) {
            $field_names[] = $name;
        }
        $this->assertContains('csrf_token', $field_names);
        $this->assertFalse($form->validate());
        $this->assertEquals('dummytoken', $form->csrf_token->value);
        $form = new F(["formdata" => ["csrf_token" => "dummytoken"]]);
        $this->assertTrue($form->validate());
    }

    public function testCSRFOff()
    {
        $form = new F(["meta" => ["csrf" => false]]);
        $field_names = [];
        foreach ($form as $name => $field) {
            $field_names[] = $name;
        }
        $this->assertNotContains('csrf_token', $field_names);
    }

    public function testRename()
    {
        $form = new F(["meta" => ["csrf_field_name" => "mycsrf"]]);
        $field_names = [];
        foreach ($form as $name => $field) {
            $field_names[] = $name;
        }
        $this->assertNotContains('csrf_token', $field_names);
        $this->assertContains('mycsrf', $field_names);
    }

    public function testNoPopulate()
    {
        $obj = (object)["a" => null, "csrf_token" => null];
        $form = new F(["a" => "test", "csrf_token" => "dummytoken"]);
        $form->populateObj($obj);
        $this->assertNull($obj->csrf_token);
        $this->assertEquals('test', $obj->a);
    }

}
