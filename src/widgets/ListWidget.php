<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/18/2016
 * Time: 2:42 PM
 */

namespace Deathnerd\WTForms\Widgets;

use Deathnerd\WTForms\Fields\Field;

class ListWidget extends Widget
{
    public $html_tag = "";
    public $prefix_label = true;

    public function __construct($html_tag = "ul", $prefix_label = true)
    {
        assert(in_array($html_tag, ['ul', 'ol']));
        $this->html_tag = $html_tag;
        $this->prefix_label = $prefix_label;
    }

    public function __invoke(Field $field, $kwargs = [])
    {
        $kwargs['id'] = is_null($kwargs['id']) ? $field->id : $kwargs['id'];
        $html = ["<$this->html_tag " . html_params($kwargs) . ">"];
        foreach ($field as $subfield) {
            if ($this->prefix_label) {
                $html[] = "<li>$subfield->label {$subfield->call()}</li>";
            } else {
                $html[] = "<li>{$subfield->call()} $subfield->label</li>";
            }
        }
        $html[] = "</$this->html_tag>";
        return implode("", $html);
    }
}