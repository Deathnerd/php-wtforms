<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/18/2016
 * Time: 3:46 PM
 */

namespace WTForms\Widgets\Core;

use WTForms\Exceptions\NotImplemented;
use WTForms\Fields\Core\Field;

/**
 * Just so I can have type hinting for widgets
 * @package WTForms\Widgets
 */
class Widget
{
    public $field_flags = [];

    /**
     * @param Field|mixed $field
     * @param array       $options
     *
     * @throws NotImplemented
     */
    public function __invoke($field, array $options = [])
    {
        throw new NotImplemented("Widget needs an __invoke method");
    }
}
