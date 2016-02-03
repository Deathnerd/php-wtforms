<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/3/2016
 * Time: 4:57 PM
 */

namespace Deathnerd\WTForms\Widgets\Core;

use Deathnerd\WTForms\Fields\Core\Field;
use Illuminate\Support\HtmlString;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Renders a multi-line text area.
 *
 * `rows` and `cols` ought to be passed as members of the `$kwargs` array when
 * rendering
 * @package Deathnerd\WTForms\Widgets\Core
 */
class TextArea extends Widget
{
    /**
     * @param Field $field
     * @param array $kwargs
     * @return HtmlString
     */
    public function __invoke(Field $field, array $kwargs = [])
    {
        $kwargs = (new OptionsResolver())->setDefault("id", $field->id)->resolve($kwargs);
        $kwargs['name'] = $field->name;
        $html_params = html_params($kwargs);
        $escaped_content = htmlspecialchars($field->_value(), ENT_NOQUOTES, 'UTF-8');
        $html = sprintf("<textarea %s>%s</textarea>", $html_params, $escaped_content);

        return new HtmlString($html);
    }

}