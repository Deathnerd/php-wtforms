<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/15/2016
 * Time: 9:56 PM
 */

namespace WTForms\Annotations\Fields\Core;

use WTForms\Annotations\Field;

/**
 * @Annotation
 */
class SelectFieldBase extends Field
{
  /**
   * @var string
   */
  public $type = "text";
  /**
   * @var string
   */
  public $widget = 'WTForms\Widgets\Core\Select';

  /**
   * @var string
   */
  public $option_widget;
}