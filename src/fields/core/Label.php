<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 3:07 PM
 */

namespace WTForms\Fields\Core;


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
    return (string)$this->__invoke();
  }

  public function __invoke($text = "", array $options = [])
  {
    $options = array_merge(["for" => $this->field_id], $options);

    return sprintf("<label %s>%s</label>", html_params($options), $text ?: $this->text);
  }
}
