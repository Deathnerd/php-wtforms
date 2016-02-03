<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 3:07 PM
 */

namespace Deathnerd\WTForms\Fields\Core;


use Illuminate\Support\HtmlString;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        } else {
            $kwargs = (new OptionsResolver())->setDefault('for', $this->field_id)->resolve($kwargs);
        }
        return new HtmlString(sprintf("<label %s>%s</label>", html_params($kwargs),$text ?: $this->text));
    }
}
