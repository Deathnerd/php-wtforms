<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 5:17 PM
 */

namespace Deathnerd\WTForms\Widgets\HTML5;

use Deathnerd\WTForms\Widgets\Core\Input;

/**
 * Renders an input with type "datetime"
 * @package Deathnerd\WTForms\Widgets\HTML5
 */
class DateTimeInput extends Input
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct("datetime");
    }
}
