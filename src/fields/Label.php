<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 3:07 PM
 */

namespace Deathnerd\WTForms\Fields;


use Illuminate\Support\HtmlString;

class Label
{
    public $field_id = "";
    public $text = "";

    public function __construct($field_id, $text)
    {
        $this->field_id = $field_id;
        $this->text = $text;
    }

    public function __toString()
    {
        return "Label($this->field_id,$this->text)";
    }

    public function __invoke($text = "", array $kwargs = [])
    {
        if (array_key_exists("for_", $kwargs)) {
            $kwargs["for"] = $kwargs["for_"];
            unset($kwargs["for_"]);
        } else if (!array_key_exists("for", $kwargs)) {
            $kwargs['for'] = $this->field_id;
        }
        $attributes = html_params($kwargs);
        return new HtmlString("<label $attributes>" . $text or $this->text . "</label>");
    }
}