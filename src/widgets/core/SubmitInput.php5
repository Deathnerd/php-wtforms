<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/3/2016
 * Time: 4:46 PM
 */

namespace Deathnerd\WTForms\Widgets\Core;

use Deathnerd\WTForms\Fields\Core\Field;
use Illuminate\Support\HtmlString;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Renders a submit button.
 *
 * The field's label is used as the text of the submit button instead of the
 * data on the field.
 *
 * @package Deathnerd\WTForms\Widgets\Core
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
     * @param array $kwargs
     * @return HtmlString
     */
    public function __invoke(Field $field, array $kwargs = [])
    {
        $kwargs = (new OptionsResolver())->setDefault("value", $field->label->text)->resolve($kwargs);
        return parent::__invoke($field, $kwargs);
    }
}