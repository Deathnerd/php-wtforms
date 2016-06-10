<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 9:09 PM
 */

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Render a checkbox.
 *
 * The ``checked`` HTML attribute is set if the field's data is a non-false value.
 * @package WTForms\Widgets\Core
 */
class CheckboxInput extends Input
{
    /**
     * CheckboxInput constructor.
     */
    public function __construct()
    {
        parent::__construct("checkbox");
    }


    /**
     * @param Field $field
     * @param array $options
     *
     * @return string|void
     */
    public function __invoke($field, array $options = [])
    {
        if ($field->checked or $field->data) {
            $options['checked'] = true;
        }

        return parent::__invoke($field, $options);
    }
}
