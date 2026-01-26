<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 4:49 PM.
 */

namespace WTForms\Fields\Simple;

use WTForms\Fields\Core\StringField;
use WTForms\Widgets\Core\TextArea;

/**
 * This field represents an HTML ``<textarea>`` and can be used to take
 * multi-line input.
 */
class TextAreaField extends StringField
{
  /**
   * @param array<string, mixed> $options
   */
  public function __construct(array $options = [])
  {
    $options = array_merge(['widget' => new TextArea()], $options);
    parent::__construct($options);
  }
}
