<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/20/2016
 * Time: 10:02 PM
 */

namespace Deathnerd\WTForms\Fields;


use Deathnerd\WTForms\Widgets\HiddenInput;

class HiddenField extends StringField
{
    public function __construct($label, array $kwargs)
    {
        parent::__construct($label, $kwargs);
        $this->widget = new HiddenInput();
    }
}