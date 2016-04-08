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
 */
class RadioField extends SelectField
{
  /**
   * @var string
   */
  public $type = "radio";
  /**
   * @var string
   */
  public $widget = 'WTForms\Widgets\Core\ListWidget';
}