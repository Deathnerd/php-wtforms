<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 3:14 PM
 */

namespace WTForms\Fields\HTML5;

use WTForms\Form;
use WTForms\Widgets\HTML5\DateInput;

/**
 * Represents an ``<input type="date">``.
 * @package WTForms\Fields\HTML5
 */
class DateField extends \WTForms\Fields\Core\DateField
{
  /**
   * @inheritdoc
   */
  public function __construct(array $options = [], Form $form = null)
  {
    parent::__construct($options, $form);
    $this->widget = new DateInput();
  }
}
