<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:23 PM
 */

namespace WTForms\Widgets\HTML5;

use WTForms\Widgets\Core\Input;

/**
 * Renders an input with type "tel"
 * @package WTForms\Widgets\HTML5
 */
class TelInput extends Input
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct("tel");
    }
}
