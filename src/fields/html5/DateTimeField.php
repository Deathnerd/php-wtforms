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
   * @param array  $options
   */
  public function __construct($label = "", array $options = [])
  {
    parent::__construct($label, $options);
    $this->widget = new DateTimeInput();
  }
}
