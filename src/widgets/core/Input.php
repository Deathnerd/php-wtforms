<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/20/2016
 * Time: 10:10 PM
 */

namespace Deathnerd\WTForms\Widgets\Core;

use Deathnerd\WTForms\Fields\Core\Field;
use Illuminate\Support\HtmlString;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Render a basic ``<input>`` field
 *
 * This is used as the basis for most other input fields.
 *
 * By default, the `_value()` method will be called upon teh associated field
 * to provide the ``value=`` HTML attribute
 * @package Deathnerd\WTForms\Widgets
 */
class Input extends Widget
{
    /**
     * @var string
     */
    public $input_type;

    public function __construct($input_type = "")
    {
        if ($input_type !== "") {
            $this->input_type = $input_type;
        }
    }

    public function __invoke(Field $field, array $kwargs = [])
    {
        $kwargs = (new OptionsResolver())->setDefaults([
            "id" => $field->id,
            "type" => $this->input_type,
            "value" => $field->_value()
        ])->resolve($kwargs);
        $kwargs['name'] = $field->name;
        return new HtmlString("<input " . html_params($kwargs) . ">");
    }
}
