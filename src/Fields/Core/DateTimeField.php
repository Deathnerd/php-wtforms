<?php

declare(strict_types=1);

namespace WTForms\Fields\Core;

use Carbon\Carbon;
use WTForms\Exceptions\ValueError;
use WTForms\Fields\Options\DateTimeFieldOptions;
use WTForms\Widgets\Core\TextInput;

/**
 * A text field which stores a `DateTime` matching a format.
 *
 * @phpstan-import-type DateTimeFieldOptionsArray from DateTimeFieldOptions
 *
 * @extends Field<\Carbon\Carbon|\DateTime|null>
 */
class DateTimeField extends Field
{
  /**
   * The strftime-style format string for parsing/displaying dates.
   */
  public string $format = '%Y-%m-%d %H:%i:%s';

  /**
   * The Carbon/PHP format string (without % prefixes).
   */
  protected string $carbon_format = 'Y-m-d H:i:s';

  /**
   * @param DateTimeFieldOptionsArray $options
   */
  public function __construct(array $options = [])
  {
    if (array_key_exists('format', $options)) {
      $this->format = $options['format'];
      unset($options['format']);
    }
    $this->carbon_format = preg_replace('/%/', '', $this->format) ?? $this->carbon_format;
    $options = array_merge(['widget' => new TextInput()], $options);
    parent::__construct($options);
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
        return $this->data->format($this->carbon_format);
      } elseif ($this->data instanceof \DateTime) {
        return Carbon::instance($this->data)->format($this->carbon_format);
      }

      return ''; // @codeCoverageIgnore
    }

    return parent::__get($name); // @codeCoverageIgnore
  }

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
        $this->data = Carbon::createFromFormat($this->carbon_format, $date_str);
      } catch (\Exception $e) {
        $this->data = null;
        throw new ValueError('Not a valid datetime value');
      }
    }
  }
}
