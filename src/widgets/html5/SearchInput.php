<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:13 PM
 */

namespace WTForms\Widgets\HTML5;

use WTForms\Widgets\Core\Input;

/**
 * Renders an input type with "search".
 * @package WTForms\Widgets\HTML5
 */
class SearchInput extends Input
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct("search");
    }
}
