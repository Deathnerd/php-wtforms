<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/15/2016
 * Time: 9:56 PM
 */

namespace Deathnerd\WTForms\Fields\Core\Annotations;

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
  public $widget = 'Deathnerd\WTForms\Widgets\Core\TextInput';
}