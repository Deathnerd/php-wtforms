<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/18/2016
 * Time: 2:42 PM
 */

namespace Deathnerd\WTForms\Widgets\Core;

use Deathnerd\WTForms\Fields\Core\Field;
use Illuminate\Support\HtmlString;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * REnders a list of fields as `ul` or `ol` list.
 *
 * This is used for fields which encapsulate many inner fields as subfields.
 * The widget will try to iterate the field to get access to the subfields and
 * call them to render them.
 *
 * If `$prefix_label` is set, the subfield's label is printed before the field,
 * otherwise afterwards. The latter is useful for iterating radios or
 * checkboxes.
 * @package Deathnerd\WTForms\Widgets\Core
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
     * @param string $html_tag Must either be ul or ol. Will not pass assertion otherwise
     * @param bool $prefix_label If set to true, subfields will have their labels printed before
     * them, otherwise afterwards.
     */
    public function __construct($html_tag = "ul", $prefix_label = true)
    {
        assert(in_array($html_tag, ['ul', 'ol']));
        $this->html_tag = $html_tag;
        $this->prefix_label = $prefix_label;
    }

    /**
     * @param Field $field
     * @param array $kwargs
     * @return string
     */
    public function __invoke(Field $field, $kwargs = [])
    {
        $kwargs = (new OptionsResolver())->setDefault("id", $field->id)->resolve($kwargs);
        $html = "<$this->html_tag " . html_params($kwargs) . ">";
        foreach ($field as $subfield) {
            if ($this->prefix_label) {
                $html .= "<li>$subfield->label {$subfield()}</li>";
            } else {
                $html .= "<li>{$subfield()} $subfield->label</li>";
            }
        }
        $html .= "</$this->html_tag>";
        return new HtmlString($html);
    }
}
