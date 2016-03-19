<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 2:26 PM
 */

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\SelectFieldBase;

/**
 * Renders a select field.
 *
 * If `$multiple` is true, then the `size` property should be specified on rendering
 * to make the field useful.
 *
 * The field must provide an `iter_choices()` method which the widget will
 * call on rendering; this method must yield tuples of
 * `($value, $label, $selected)`.
 *
 * @package WTForms\Widgets\Core
 */
class Select extends Widget
{
  protected $multiple = false;

  /**
   * Select constructor.
   *
   * @param bool $multiple
   */
  public function __construct($multiple = false)
  {
    $this->multiple = $multiple;
  }

  /**
   * @param SelectFieldBase $field
   * @param array           $kwargs
   *
   * @return string
   * @throws \WTForms\NotImplemented
   */
  public function __invoke(SelectFieldBase $field, array $kwargs = [])
  {
    $kwargs = array_merge(["id" => $field->id], $kwargs);
    $kwargs['name'] = $field->name;
    if ($this->multiple) {
      $kwargs['multiple'] = true;
    }

    $html = sprintf("<select %s>", html_params($kwargs));
    foreach ($field->iter_choices() as $choice) {
      $html .= self::render_option($choice["value"], $choice["label"], $choice["selected"]);
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
   * @param array   $kwargs
   *
   * @return string
   */
  public static function render_option($value, $label, $selected, $kwargs = [])
  {
    if ($value === true) {
      // Handle the special case of a true value
      $value = "true";
    }
    $options = array_merge($kwargs, ["value" => $value]);
    if ($selected) {
      $options['selected'] = true;
    }

    return sprintf("<option %s>%s</option>", html_params($options), e($label));
  }
}
