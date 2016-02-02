<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:21 PM
 */

namespace Deathnerd\WTForms\Fields\HTML5;

use Deathnerd\WTForms\Fields\Core\StringField;

/**
 * Represents an ``<input type="tel">``.
 * @package Deathnerd\WTForms\Fields\HTML5
 */
class TelField extends StringField
{
    /**
     * @inheritdoc
     */
    public function __construct($label, array $kwargs)
    {
        parent::__construct($label, $kwargs);
        $this->widget = new \Deathnerd\WTForms\Widgets\HTML5\TelInput();
    }

}
