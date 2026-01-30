<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:25 PM.
 */

namespace WTForms\Fields\HTML5;

use WTForms\Fields\Core\StringField;
use WTForms\Fields\Options\FieldOptions;
use WTForms\Widgets\HTML5\URLInput;

/**
 * Represents an ``<input type="url">``.
 * @phpstan-import-type FieldOptionsArray from FieldOptions
 */
class URLField extends StringField
{
  /**
   * @phpstan-param FieldOptionsArray $options
   *
   * @phpstan-param array<string, mixed> $options
   */
  public function __construct(array $options = [])
  {
    $options = array_merge(['widget' => new URLInput()], $options);
    parent::__construct($options);
  }
}
