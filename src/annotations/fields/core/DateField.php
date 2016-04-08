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
class DateField extends StringField
{
  /**
   * @var string
   */
  public $type = "date";
  
  /**
   * @var string The format for the date to be kept in
   */
  public $format = "Y-m-d";
}