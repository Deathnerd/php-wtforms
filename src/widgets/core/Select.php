<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 2:26 PM
 */

namespace Deathnerd\WTForms\Widgets\Core;

use Deathnerd\WTForms\Fields\Core\SelectFieldBase;
use Illuminate\Support\HtmlString;

class Select extends Widget
{
    protected $multiple = false;

    /**
     * Select constructor.
     * @param bool $multiple
     */
    public function __construct($multiple = false)
    {
        $this->multiple = $multiple;
    }

    public function call(SelectFieldBase $field, array $kwargs = [])
    {
        if (!is_null($kwargs['id'])) {
            $kwargs['id'] = $field->id;
        }
        if ($this->multiple) {
            $kwargs['multiple'] = true;
        }
        $kwargs['name'] = $field->name;
        $html = ["<select " . html_params($kwargs) . ">"];
        foreach ($field->iter_choices() as list($val, $label, $selected)) {
            $html[] = self::render_option($val, $label, $selected);
        }
        $html[] = "</select>";
        return new HtmlString(implode("", $html));
    }

    /**
     * Private method called when rendering each option as HTML
     * @param $value
     * @param $label
     * @param $selected
     * @param array $kwargs
     * @return HtmlString
     */
    public static function render_option($value, $label, $selected, $kwargs = [])
    {
        if ($value == true) {
            // Handle the special case of a true value
            $value = "true";
        }
        $options = array_merge($kwargs, ["value" => $value]);
        if ($selected) {
            $options['selected'] = true;
        }
        return new HtmlString("<option " . html_params($options) . ">" . e($label) . "</option>");
    }
}
