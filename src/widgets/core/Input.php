<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/20/2016
 * Time: 10:10 PM
 */

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Render a basic ``<input>`` field
 *
 * This is used as the basis for most other input fields.
 *
 * By default, the `_value()` method will be called upon teh associated field
 * to provide the ``value=`` HTML attribute
 * @package WTForms\Widgets
 */
class Input extends Widget
{
    /**
     * @var string
     */
    public $input_type;

    /**
     * @param string $input_type If passed, will add ``type="$input_type"`` to the
     *                           html attributes for this input
     */
    public function __construct($input_type = "")
    {
        if ($input_type !== "") {
            $this->input_type = $input_type;
        }
    }

    /**
     * @param Field $field
     * @param array $options
     *
     * @return string
     */
    public function __invoke($field, array $options = [])
    {
        $defaults = [
            "id"    => $field->id,
            "type"  => $this->input_type,
            "value" => $field->value
        ];
        $options = array_merge($defaults, $options);
        $options['name'] = $field->name;

        return sprintf("<input %s>", html_params($options));
    }
}
