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
class BooleanField extends Field
{
  /**
   * @var string
   */
  public $type = "checkbox";
  /**
   * @var string
   */
  public $widget = 'WTForms\Widgets\Core\CheckboxInput';

  /**
   * @var array An array of string representations of values to be considered "falsey" 
   */
  public $false_values = ['false', ''];

  /**
   * @var boolean
   */
  public $default;
}