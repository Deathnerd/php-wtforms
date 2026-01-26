<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 3:14 PM.
 */

namespace WTForms\Fields\Core;

use Carbon\Carbon;
use WTForms\Exceptions\ValueError;

/**
 * Same as DateTimeField, except stores a date (actually still a DateTime,
 * but formats to just a Date).
 */
class DateField extends DateTimeField
{
  public $format = '%Y-%m-%d';

  /**
   * @throws ValueError
   */
  public function processFormData(array $valuelist)
  {
    if ($valuelist) {
      $date_str = implode(' ', $valuelist);
      try {
        $this->data = Carbon::createFromFormat($this->carbon_format, $date_str)->startOfDay();
      } catch (\Exception $e) {
        $this->data = null;
        // Do not create a process error if this is an optional field
        if (!empty($date_str) && !$this->flags->optional) {
          throw new ValueError('Not a valid date value');
        }
      }
    }
  }

  public function __get($name)
  {
    if ('value' == $name) {
      if ($this->raw_data) {
        return implode(' ', $this->raw_data);
      }

      if ($this->data instanceof Carbon) {
        return $this->data->startOfDay()->format($this->carbon_format);
      } elseif ($this->data instanceof \DateTime) {
        return Carbon::instance($this->data)->startOfDay()->format($this->carbon_format);
      }

      return '';
    }

    return parent::__get($name); // @codeCoverageIgnore
  }
}
