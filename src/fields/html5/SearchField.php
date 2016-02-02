<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:09 PM
 */

namespace Deathnerd\WTForms\Fields\HTML5;
use Deathnerd\WTForms\Fields\Core\StringField;
use Deathnerd\WTForms\Widgets\HTML5\SearchInput;

/**
 * Represents an ``<input type="search">``.
 * @package Deathnerd\WTForms\Fields\HTML5
 */
class SearchField extends StringField
{
    /**
     * @inheritdoc
     */
    public function __construct($label = "", array $kwargs = [])
    {
        $this->widget = new SearchInput();
        parent::__construct($label, $kwargs);
    }
}
