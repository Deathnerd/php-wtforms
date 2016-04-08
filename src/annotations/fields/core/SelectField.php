<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/15/2016
 * Time: 9:56 PM
 */

namespace WTForms\Annotations\Fields\Core;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class SelectField extends SelectFieldBase
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
  public $option_widget = 'WTForms\Widgets\Core\Option';
}