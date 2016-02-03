<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 5:00 PM
 */

namespace Deathnerd\WTForms\Fields\Simple;

use Deathnerd\WTForms\Fields\Core\StringField;
use Deathnerd\WTForms\Widgets\Core\HiddenInput;

/**
 * HiddenField is a convenience for a StringField with a HiddenInput widget.
 *
 * It will render as an ``<input type="hidden">`` but otherwise coerce to a string.
 * @package Deathnerd\WTForms\Fields\Simple
 */
class HiddenField extends StringField
{
    /**
     * @inheritdoc
     */
    public function __construct($label = "", array $kwargs = [])
    {
        parent::__construct($label, $kwargs);
        $this->widget = new HiddenInput();
    }
}
