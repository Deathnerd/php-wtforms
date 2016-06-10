<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 2:26 PM
 */

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;
use WTForms\Fields\Core\SelectField;

/**
 * Renders a select field.
 *
 * If `$multiple` is true, then the `size` property should be specified on rendering
 * to make the field useful.
 *
 * The field must provide a `getChoices()` method which the widget will
 * call on rendering; this method must yield tuples of
 * `($value, $label, $selected)`.
 *
 * @package WTForms\Widgets\Core
 */
class Select extends Widget
{
    public $multiple = false;

    /**
     * @param Field|SelectField $field
     * @param array             $options
     *
     * @return string
     */
    public function __invoke($field, array $options = [])
    {
        $options = array_merge(["id" => $field->id], $options);
        $options['name'] = $field->name;
        if ($this->multiple) {
            $options['multiple'] = true;
        }

        $html = sprintf("<select %s>", html_params($options));
        foreach ($field->getChoices() as list($value, $label, $selected)) {
            $html .= self::renderOption($value, $label, $selected);
        }
        $html .= "</select>";

        return $html;
    }

    /**
     * Private method called when rendering each option as HTML
     *
     * @param mixed   $value
     * @param string  $label
     * @param boolean $selected
     * @param array   $options
     *
     * @return string
     */
    public static function renderOption($value, $label, $selected, $options = [])
    {
        if ($value === true) {
            // Handle the special case of a true value
            $value = "true";
        }
        $options = array_merge($options, ["value" => $value]);
        if ($selected) {
            $options['selected'] = true;
        }

        return sprintf("<option %s>%s</option>", html_params($options), $label);
    }
}
