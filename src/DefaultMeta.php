<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/31/2015
 * Time: 11:34 AM
 */

namespace WTForms;

use WTForms\Fields;
use WTForms\Fields\Core\Field;

/**
 * This is the default Meta class which defines all the default values
 * and therefore is also the 'API' of the class Meta interface
 * @package WTForms
 */
class DefaultMeta
{
  /**
   * @var string
   */
  public $csrf_field_name = "csrf_token";
  /**
   * @var string
   */
  public $csrf_time_limit;
  /**
   * @var bool
   */
  public $csrf = false;
  /**
   * @var null
   */
  public $csrf_secret = null;
  /**
   * @var null|array|object
   */
  public $csrf_context = null;
  /**
   * @var null|callable
   */
  public $csrf_class = null;

  /**
   * @param Field $field
   * @param array $render_kw
   *
   * @return mixed
   */
  public function render_field(Field $field, $render_kw = [])
  {
    $other_kw = property_exists($field, 'render_kw') ? $field->render_kw : null;
    if (!is_null($other_kw)) {
      $render_kw = array_merge($other_kw, $render_kw);
    }
    $widget = $field->widget;

    return $widget($field, $render_kw);
  }
}
