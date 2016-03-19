<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 4:30 PM
 */

namespace WTForms\Fields\HTML5;

use WTForms\Fields\Core\DecimalField;
use WTForms\Widgets\HTML5\RangeInput;

/**
 * Represents an ``<input type="range">``.
 * @package WTForms\Fields\HTML5
 */
class DecimalRangeField extends DecimalField
{
  /**
   * @inheritdoc
   */
  public function __construct($label = "", array $options = [])
  {
    parent::__construct($label, $options);
    $this->widget = new RangeInput(["step" => "any"]);
  }
}
