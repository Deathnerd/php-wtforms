<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:09 PM
 */

namespace WTForms\Fields\HTML5;

use WTForms\Fields\Core\StringField;
use WTForms\Widgets\HTML5\SearchInput;

/**
 * Represents an ``<input type="search">``.
 * @package WTForms\Fields\HTML5
 */
class SearchField extends StringField
{
  /**
   * @inheritdoc
   */
  public function __construct($label = "", array $options = [])
  {
    parent::__construct($label, $options);
    $this->widget = new SearchInput();
  }
}
