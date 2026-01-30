<?php

declare(strict_types=1);

namespace WTForms\Fields\Core;

use Carbon\Carbon;
use DateTime;
use Exception;
use WTForms\Exceptions\ValueError;
use WTForms\Fields\Options\DateTimeFieldOptions;

/**
 * Same as DateTimeField, except stores a date (actually still a DateTime,
 * but formats to just a Date).
 *
 * @phpstan-import-type DateTimeFieldOptionsArray from DateTimeFieldOptions
 */
class DateField extends DateTimeField
{
  public string $format = '%Y-%m-%d';

  /**
   * @param array<int, string> $valuelist
   *
   * @throws ValueError
   */
  public function processFormData(array $valuelist): void
  {
    if ($valuelist) {
      $date_str = implode(' ', $valuelist);
      try {
        $this->data = Carbon::createFromFormat($this->carbon_format, $date_str)->startOfDay();
      } catch (Exception $e) {
        $this->data = null;
        // Do not create a process error if this is an optional field
        if(!$this->flags->optional) {
          throw new ValueError(message: 'Not a valid date value', previous: $e);
        }
      }
    }
  }

  /**
   * @return string
   */
  public function __get(string $name)
  {
    if ('value' == $name) {
      if ($this->raw_data) {
        return implode(' ', $this->raw_data);
      }

      if ($this->data instanceof Carbon) {
        return $this->data->startOfDay()->format($this->carbon_format);
      } elseif ($this->data instanceof DateTime) {
        return Carbon::instance($this->data)->startOfDay()->format($this->carbon_format);
      }

      return '';
    }

    return parent::__get($name); // @codeCoverageIgnore
  }
}
