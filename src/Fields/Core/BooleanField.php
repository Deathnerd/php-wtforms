<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 8:59 PM.
 */

namespace WTForms\Fields\Core;

use WTForms\Fields\Options\FieldOptions;
use WTForms\Widgets\Core\CheckboxInput;

/**
 * Represents an ``<input type="checkbox">``. Set the ``checked``-status by using the
 * ``default``-option. Any value for ``default``, e.g. ``default="checked"`` puts
 * ``checked`` into the html-element and sets the ``data`` to ``true``.
 *
 * @phpstan-import-type FieldOptionsArray from FieldOptions
 * @phpstan-type BooleanFieldDataType bool
 *
 * @phpstan-type BooleanFieldOptionsArray array{false_values?: array<int, string>} & FieldOptionsArray
 *
 * @extends Field<BooleanFieldDataType>
 */
class BooleanField extends Field
{
  /**
   * A sequence of strings to be considered "false" values for the field.
   *
   * @var array<int, string>
   */
  public array $false_values = ['false', ''];

  public bool $data = false;

  /**
   * BooleanField constructor.
   *
   * @phpstan-param BooleanFieldOptionsArray $options
   */
  public function __construct(array $options = [])
  {
    $options = array_merge(['widget' => new CheckboxInput()], $options);
    if (array_key_exists('false_values', $options)) {
      $this->false_values = $options['false_values'];
      unset($options['false_values']);
    }
    parent::__construct($options);
  }

  /**
   * Process the data applied to this field and store the result.
   *
   * This will be called during form construction by the form's `options` or
   * `obj` argument.
   *
   * @param string|array|bool|null $value
   */
  public function processData($value): void
  {
    $this->data = boolval($value);
  }

  /**
   * Process data received over the wire from a form.
   *
   * @param array<int, string> $valuelist A list of strings to process
   */
  public function processFormData(array $valuelist): void
  {
    if (!$valuelist || in_array($valuelist[0], $this->false_values)) {
      $this->data = false;
    } else {
      $this->data = true;
    }
  }

  /**
   * @return string|null
   *
   * @internal
   */
  public function __get(string $name)
  {
    if ('value' == $name) {
      if (!empty($this->raw_data)) {
        return strval($this->raw_data[0]);
      }

      return 'y';
    }

    return null;
  }
}
