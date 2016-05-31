<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 3:14 PM
 */

namespace WTForms\Fields\Core;

/**
 * Same as DateTimeField, except stores a date (actually still a DateTime,
 * but formats to just a Date).
 * @package WTForms\Fields\Core
 */
class DateField extends DateTimeField
{

  public $format = "Y-m-d";
}
