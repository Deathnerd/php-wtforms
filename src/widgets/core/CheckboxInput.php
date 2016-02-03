<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 9:09 PM
 */

namespace Deathnerd\WTForms\Widgets\Core;

use Deathnerd\WTForms\Fields\Core\Field;
use Illuminate\Support\HtmlString;

/**
 * Render a checkbox.
 *
 * The ``checked`` HTML attribute is set if the field's data is a non-false value.
 * @package Deathnerd\WTForms\Widgets\Core
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
     * @param array $kwargs
     * @return HtmlString|void
     */
    public function __invoke(Field $field, array $kwargs = [])
    {
        if ($field->checked or $field->data) {
            $kwargs['checked'] = true;
        }
        return parent::__invoke($field, $kwargs);
    }
}
