<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/20/2016
 * Time: 10:02 PM
 */

namespace Deathnerd\WTForms\Fields\Core;

use Deathnerd\WTForms\Widgets;
use Deathnerd\WTForms\Widgets\Core\TextInput;

/**
 * This field is the base for most of the more complicated fields, and
 * represents an ``<input type="text">``.
 * @package Deathnerd\WTForms\Fields
 */
class StringField extends Field
{
    /**
     * @var TextInput
     */
    public $widget;

    public function __construct($label, array $kwargs)
    {
        parent::__construct($label, $kwargs);
        $this->widget = new TextInput();
    }

    /**
     * @param array $valuelist
     */
    public function process_formdata(array $valuelist = [])
    {
        if (!empty($valuelist)) {
            $this->data = $valuelist[0];
        } else {
            $this->data = '';
        }
    }

    public function _value()
    {
        return (!empty($this->data)) ? $this->data : '';
    }
}
