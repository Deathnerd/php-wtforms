<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:35 PM
 */

namespace WTForms\Fields\HTML5;

use WTForms\Widgets\HTML5\DateTimeInput;

/**
 * Represents an ``<input type="datetime">``.
 * @package WTForms\Fields\HTML5
 */
class DateTimeField extends \WTForms\Fields\Core\DateTimeField
{
  /**
   * DateTimeField constructor.
   *
   * @param string $label
   * @param array  $kwargs
   */
  public function __construct($label = "", array $kwargs = [])
  {
    parent::__construct($label, $kwargs);
    $this->widget = new DateTimeInput();
  }
}
