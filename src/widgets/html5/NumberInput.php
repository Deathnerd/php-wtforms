<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 3:48 PM
 */

namespace WTForms\Widgets\HTML5;

use WTForms\Fields\Core\Field;
use WTForms\Widgets\Core\Input;

/**
 * Renders an input with type "number".
 * @package WTForms\Widgets\HTML5
 */
class NumberInput extends Input
{
  public $min;
  public $max;
  public $step;

  /**
   * @inheritdoc
   */
  public function __construct(array $kwargs = [])
  {
    $kwargs = array_merge(["step" => null, "min" => null, "max" => null], $kwargs);
    $this->step = $kwargs['step'];
    $this->min = $kwargs['min'];
    $this->max = $kwargs['max'];
    parent::__construct("number");
  }

  /**
   * @inheritdoc
   */
  public function __invoke(Field $field, array $kwargs = [])
  {
    $kwargs = array_merge(["step" => $this->step, "min" => $this->min, "max" => $this->max], $kwargs);

    return parent::__invoke($field, $kwargs);
  }

}
