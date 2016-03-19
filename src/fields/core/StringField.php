<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/20/2016
 * Time: 10:02 PM
 */

namespace WTForms\Fields\Core;

use WTForms\Widgets;
use WTForms\Widgets\Core\TextInput;

/**
 * This field is the base for most of the more complicated fields, and
 * represents an ``<input type="text">``.
 * @package WTForms\Fields
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
    public function process_formdata(array $valuelist)
    {
        if ($valuelist) {
            $this->data = $valuelist[0];
        } else {
            $this->data = '';
        }
    }

    public function _value()
    {
        if ($this->data) {
            return $this->data;
        }
        return '';
    }
}
