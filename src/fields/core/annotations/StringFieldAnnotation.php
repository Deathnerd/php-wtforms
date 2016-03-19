<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/15/2016
 * Time: 9:56 PM
 */

namespace WTForms\Fields\Core\Annotations;

/**
 * Class StringFieldAnnotation
 */
class StringFieldAnnotation extends FieldAnnotation
{
  /**
   * @var string
   */
  public $type = "text";
  /**
   * @var string
   */
  public $widget = 'WTForms\Widgets\Core\TextInput';
}