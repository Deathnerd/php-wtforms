<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/20/2016
 * Time: 10:05 PM
 */

namespace WTForms\Widgets\Core;


/**
 * Render a single-line text input
 *
 * @package WTForms\Widgets
 */
class TextInput extends Input
{
    public function __construct()
    {
        parent::__construct("text");
    }
}
