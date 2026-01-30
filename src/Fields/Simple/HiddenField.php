<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 5:00 PM.
 */

namespace WTForms\Fields\Simple;

use WTForms\Fields\Core\StringField;
use WTForms\Widgets\Core\HiddenInput;

/**
 * HiddenField is a convenience for a StringField with a HiddenInput widget.
 *
 * It will render as an ``<input type="hidden">`` but otherwise coerce to a string.
 */
class HiddenField extends StringField
{
  /**
   * @param array<string, mixed> $options
   */
  public function __construct(array $options = [])
  {
    $options = array_merge(['widget' => new HiddenInput()], $options);
    parent::__construct($options);
  }

  protected function postHydrate(): void
  {
    if (!$this->widget) {
      $this->widget = new HiddenInput();
    }
  }
}
