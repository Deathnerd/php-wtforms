<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/3/2016
 * Time: 4:57 PM
 */

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Renders a multi-line text area.
 *
 * `rows` and `cols` ought to be passed as members of the `$options` array when
 * rendering
 * @package WTForms\Widgets\Core
 */
class TextArea extends Widget
{
    /**
     * @param Field $field
     * @param array $options
     *
     * @return string
     */
    public function __invoke($field, array $options = [])
    {
        $options = array_merge(["id" => $field->id], $options);
        $options['name'] = $field->name;

        return sprintf("<textarea %s>%s</textarea>", html_params($options), e($field->value));
    }
}
