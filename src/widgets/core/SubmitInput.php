<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/3/2016
 * Time: 4:46 PM
 */

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Renders a submit button.
 *
 * The field's label is used as the text of the submit button instead of the
 * data on the field.
 *
 * @package WTForms\Widgets\Core
 */
class SubmitInput extends Input
{
    /**
     * SubmitInput constructor.
     */
    public function __construct()
    {
        parent::__construct("submit");
    }

    /**
     * @param Field $field
     * @param array $options
     *
     * @return string
     */
    public function __invoke($field, array $options = [])
    {
        return parent::__invoke($field, array_merge(["value" => $field->label->text], $options));
    }
}
