<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:25 PM
 */

namespace WTForms\Fields\HTML5;

use WTForms\Fields\Core\StringField;
use WTForms\Widgets\HTML5\URLInput;

/**
 * Represents an ``<input type="url">``.
 * @package WTForms\Fields\HTML5
 */
class URLField extends StringField
{
    /**
     * @inheritdoc
     */
    public function __construct(array $options = [])
    {
        $options = array_merge(["widget" => new URLInput()], $options);
        parent::__construct($options);
    }
}
