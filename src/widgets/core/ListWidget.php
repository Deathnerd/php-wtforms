<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/18/2016
 * Time: 2:42 PM
 */

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;
use WTForms\Fields\Core\SelectFieldBase;

/**
 * Renders a list of fields as `ul` or `ol` list.
 *
 * This is used for fields which encapsulate many inner fields as subfields.
 * The widget will try to iterate the field to get access to the subfields and
 * call them to render them.
 *
 * If `$prefix_label` is set, the subfield's label is printed before the field,
 * otherwise afterwards. The latter is useful for iterating radios or
 * checkboxes.
 * @package WTForms\Widgets\Core
 */
class ListWidget extends Widget
{
    /**
     * @var string
     */
    public $html_tag = "";
    /**
     * @var bool If set to true, subfields will have their labels printed before
     * them, otherwise afterwards.
     */
    public $prefix_label = true;

    /**
     * ListWidget constructor.
     *
     * @param string $html_tag     Must either be ul or ol. Will not pass assertion otherwise
     * @param bool   $prefix_label If set to true, subfields will have their labels printed before
     *                             them, otherwise afterwards.
     */
    public function __construct($html_tag = "ul", $prefix_label = true)
    {
        assert(in_array($html_tag, ['ul', 'ol']));
        $this->html_tag = $html_tag;
        $this->prefix_label = $prefix_label;
    }

    /**
     * @param Field|mixed $field
     * @param array       $options
     *
     * @return string
     */
    public function __invoke($field, array $options = [])
    {
        $options = array_merge(["id" => $field->id], $options);
        $html = sprintf("<%s %s>", $this->html_tag, html_params($options));
        if ($field instanceof SelectFieldBase) {
            $field = $field->options;
        }
        foreach ($field as $subfield) {
            if ($this->prefix_label) {
                $html .= sprintf("<li>%s %s</li>", $subfield->label, $subfield());
            } else {
                $html .= sprintf("<li>%s %s</li>", $subfield(), $subfield->label);
            }
        }
        $html .= sprintf("</%s>", $this->html_tag);

        return $html;
    }
}
