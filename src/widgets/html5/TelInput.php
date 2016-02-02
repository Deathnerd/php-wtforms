<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:23 PM
 */

namespace Deathnerd\WTForms\Widgets\HTML5;

use Deathnerd\WTForms\Widgets\Core\Input;

/**
 * Renders an input with type "tel"
 * @package Deathnerd\WTForms\Widgets\HTML5
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
