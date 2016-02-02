<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:27 PM
 */

namespace Deathnerd\WTForms\Fields\HTML5;

use Deathnerd\WTForms\Fields\Core\StringField;
use Deathnerd\WTForms\Widgets\HTML5\EmailInput;

/**
 * Represents an ``<input type="email">``.
 * @package Deathnerd\WTForms\Fields\HTML5
 */
class EmailField extends StringField
{
    /**
     * EmailField constructor.
     * @param string $label
     * @param array $kwargs
     */
    public function __construct($label, array $kwargs)
    {
        parent::__construct($label, $kwargs);
        $this->widget = new EmailInput();
    }
}
