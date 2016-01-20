<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/31/2015
 * Time: 11:57 AM
 */

namespace Deathnerd\WTForms\Fields;
use Deathnerd\WTForms\Form;


class UnboundField
{
    public $_formfield = true;
    public static $creation_counter = 0;
    protected $_creation_counter = 0;
    protected $_field_class;

    /**
     * UnboundField constructor.
     */
    public function __construct($field_class, $args = [])
    {
        UnboundField::$creation_counter++;
        $this->_creation_counter = UnboundField::$creation_counter;
        $this->_field_class = $field_class;
    }

    public function bind(Form $form, $name, $prefix="", $translations=null)
    {
        return new $this->_field_class()
    }
}