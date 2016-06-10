<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 4:53 PM
 */

namespace WTForms\Fields\Simple;

use WTForms\Fields\Core\StringField;
use WTForms\Widgets\Core\PasswordInput;

/**
 * A StringField, except renders an ``<input type="password">``.
 *
 * Also, whatever value is accepted by this field is not rendered back
 * to the browser like normal fields.
 * @package WTForms\Fields\Simple
 */
class PasswordField extends StringField
{
    /**
     * @inheritdoc
     */
    public function __construct(array $options = [])
    {
        $options = array_merge(["widget" => new PasswordInput()], $options);
        parent::__construct($options);
    }
}
