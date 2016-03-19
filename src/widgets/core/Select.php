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
 * The field must provide a `getChoices()` method which the widget will
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
   * @param array           $options
   *
   * @return string
   * @throws \WTForms\NotImplemented
   */
  public function __invoke(SelectFieldBase $field, array $options = [])
  {
    $options = array_merge(["id" => $field->id], $options);
    $options['name'] = $field->name;
    if ($this->multiple) {
      $options['multiple'] = true;
    }

    $html = sprintf("<select %s>", html_params($options));
    foreach ($field->getChoices() as $choice) {
      $html .= self::renderOption($choice["value"], $choice["label"], $choice["selected"]);
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

    return sprintf("<option %s>%s</option>", html_params($options), e($label));
  }
}
