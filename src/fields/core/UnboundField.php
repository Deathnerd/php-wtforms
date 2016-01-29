<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/31/2015
 * Time: 11:57 AM
 */

namespace Deathnerd\WTForms\Fields\Core;

use Deathnerd\WTForms\BaseForm;
use Deathnerd\WTForms\Form;
use Illuminate\Support\Collection;


class UnboundField
{
    /**
     * @var bool
     */
    public $_formfield = true;
    public static $creation_counter = 0;
    protected $_creation_counter = 0;
    /**
     * @var string
     */
    protected $_field_class;
    protected $args = [];
    protected $kwargs = [];

    /**
     * UnboundField constructor.
     * // TODO Reflection, DI, or hack?
     * @param string $field_class
     * @param array $args
     * @param array $kwargs
     */
    public function __construct($field_class, array $args = [], array $kwargs = [])
    {
        UnboundField::$creation_counter++;
        $this->_creation_counter = UnboundField::$creation_counter;
        $this->_field_class = $field_class;
        $this->args = $args;
        $this->kwargs = $kwargs;
    }

    /**
     * @param BaseForm|Form $form
     * @param string $name
     * @param string $prefix
     * @param null $translations
     * @param array $kwargs
     * @return mixed
     */
    public function bind($form, $name, $prefix = "", $translations = null, array $kwargs = [])
    {
        $kw = array_merge($this->kwargs, ["_form" => $form, "_prefix" => $prefix, "_name" => $name, "_translations" => $translations]);
        // TODO Finish
        return new $this->_field_class();
    }

    public function __toString()
    {
        return sprintf("<UnboundField(%s, %r, %r)>", get_class($this->_field_class), Collection::make($args)->__toString(), Collection::make($kwargs)->__toString());
    }
}
