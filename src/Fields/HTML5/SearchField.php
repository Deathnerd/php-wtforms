<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:09 PM.
 */

namespace WTForms\Fields\HTML5;

use WTForms\Fields\Core\StringField;
use WTForms\Widgets\HTML5\SearchInput;

/**
 * Represents an ``<input type="search">``.
 */
class SearchField extends StringField
{
  public function __construct(array $options = [])
  {
    $options = array_merge(['widget' => new SearchInput()], $options);
    parent::__construct($options);
  }
}
