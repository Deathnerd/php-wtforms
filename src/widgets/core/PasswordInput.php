<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/3/2016
 * Time: 4:42 PM
 */

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Render a password input.
 *
 * For security purposes, this field will not reproduce the value on a form
 * submit by default. To have the value filled in, set the `$hide_value` to
 * `false`
 *
 * @package WTForms\Widgets\Core
 */
class PasswordInput extends Input
{
    /**
     * @var bool
     */
    public $hide_value;

    /**
     * PasswordInput constructor.
     *
     * @param bool $hide_value
     */
    public function __construct($hide_value = true)
    {
        parent::__construct("password");
        $this->hide_value = $hide_value;
    }

    /**
     * @param Field $field
     * @param array $options
     *
     * @return string
     */
    public function __invoke($field, array $options = [])
    {
        if ($this->hide_value) {
            $options['value'] = "";
        }

        return parent::__invoke($field, $options);
    }
}
