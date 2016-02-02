<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 5:18 PM
 */

namespace Deathnerd\WTForms\Widgets\HTML5;

use Deathnerd\WTForms\Widgets\Core\Input;

/**
 * Renders an input with type "date"
 * @package Deathnerd\WTForms\Widgets\HTML5
 */
class DateInput extends Input
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct("date");
    }
}
