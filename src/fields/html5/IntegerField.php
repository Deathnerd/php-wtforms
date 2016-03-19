<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 3:45 PM
 */

namespace WTForms\Fields\HTML5;

use WTForms\Widgets\HTML5\NumberInput;

/**
 * Represents an ``<input type="number">``.
 * @package WTForms\Fields\HTML5
 */
class IntegerField extends \WTForms\Fields\Core\IntegerField
{
  /**
   * @inheritdoc
   */
  public function __construct($label = "", array $kwargs = [])
  {
    parent::__construct($label, $kwargs);
    $this->widget = new NumberInput(["step" => "1"]);
  }
}
