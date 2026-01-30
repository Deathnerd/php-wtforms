<?php

declare(strict_types=1);

namespace WTForms\Fields\Core;

use WTForms\Fields\Options\SelectFieldOptions;
use WTForms\Widgets\Core\ListWidget;
use WTForms\Widgets\Core\RadioInput;

/**
 * Like a SelectField, except displays a list of radio buttons.
 *
 * Iterating a field will produce subfields (each containing a label as
 * well) in order to allow custom rendering of the individual radio fields.
 *
 * @phpstan-import-type SelectFieldOptionsArray from SelectFieldOptions
 * @phpstan-type RadioFieldOptionsArray SelectFieldOptionsArray
 */
class RadioField extends SelectField
{
  /**
   * @phpstan-param SelectFieldOptionsArray $options
   */
  public function __construct(array $options = ['choices' => []])
  {
    $options = array_merge([
      'widget' => new ListWidget('ul', false),
      'option_widget' => new RadioInput()
                           ], $options);
    parent::__construct($options);
  }

  protected function postHydrate(): void
  {
    if (!$this->widget) {
      $this->widget = new ListWidget('ul', false);
    }
    if (!isset($this->option_widget)) {
      $this->option_widget = new RadioInput();
    }
  }
}
