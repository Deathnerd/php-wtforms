<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:21 PM
 */

namespace WTForms\Fields\HTML5;

use WTForms\Fields\Core\StringField;
use WTForms\Widgets\HTML5\TelInput;

/**
 * Represents an ``<input type="tel">``.
 * @package WTForms\Fields\HTML5
 */
class TelField extends StringField
{
    /**
     * @inheritdoc
     */
    public function __construct(array $options = [])
    {
        $options = array_merge(["widget" => new TelInput()], $options);
        parent::__construct($options);
    }

}
