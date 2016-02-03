<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 4:53 PM
 */

namespace Deathnerd\WTForms\Fields\Simple;

use Deathnerd\WTForms\Fields\Core\StringField;
use Deathnerd\WTForms\Widgets\Core\PasswordInput;

/**
 * A StringField, except renders an ``<input type="password">``.
 *
 * Also, whatever value is accepted by this field is not rendered back
 * to the browser like normal fields.
 * @package Deathnerd\WTForms\Fields\Simple
 */
class PasswordField extends StringField
{
    /**
     * @inheritdoc
     */
    public function __construct($label = "", array $kwargs = [])
    {
        parent::__construct($label, $kwargs);
        $this->widget = new PasswordInput();
    }
}
