<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/3/2016
 * Time: 3:31 PM
 */

namespace Deathnerd\WTForms\Widgets\Core;

use Deathnerd\WTForms\Fields\Core\Field;
use Illuminate\Support\HtmlString;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Renders a file input chooser field.
 *
 * @package Deathnerd\WTForms\Widgets\Core
 */
class FileInput extends Widget
{
    /**
     * @param Field $field
     * @param array $kwargs
     *
     * @return HtmlString
     */
    public function __invoke(Field $field, array $kwargs = [])
    {
        $kwargs = (new OptionsResolver())->setDefault('id', $field->id)->resolve($kwargs);
        $kwargs['name'] = $field->name;
        $kwargs['type'] = "file";
        return new HtmlString(sprintf("<input %s>", html_params($kwargs)));
    }

}