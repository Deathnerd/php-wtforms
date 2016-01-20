<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 2:11 PM
 */

namespace Deathnerd\WTForms\Widgets;

use Deathnerd\WTForms\Fields\Field;

class Option
{
    public function call(Field $field, array $kwargs=[])
    {
        return Select::render_option($field->_value(), $field->label->text, $field->checked, $kwargs);
    }
}