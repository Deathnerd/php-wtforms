<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/20/2016
 * Time: 10:02 PM.
 */

namespace WTForms\Fields\Core;

use WTForms\Fields\Options\FieldOptions;
use WTForms\Widgets\Core\TextInput;

/**
 * This field is the base for most of the more complicated fields, and
 * represents an ``<input type="text">``.
 *
 * @extends Field<string>
 * @phpstan-import-type FieldOptionsArray from FieldOptions
 * @property string $data
 */
class StringField extends Field
{
  /**
   * @phpstan-param FieldOptionsArray $options
   */
  public function __construct(array $options = [])
  {
    $options = array_merge(['widget' => new TextInput()], $options);
    parent::__construct($options);
  }

  protected function postHydrate(): void
  {
    if (!$this->widget) {
      $this->widget = new TextInput();
    }
  }

  /**
   * Process data received over the wire from a form.
   *
   * @param array<int, string> $valuelist A list of strings to process
   */
  public function processFormData(array $valuelist): void
  {
    if ($valuelist) {
      $this->data = $valuelist[0];
    } else {
      $this->data = '';
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
      return $this->data ?: '';
    }

    return null;
  }
}
