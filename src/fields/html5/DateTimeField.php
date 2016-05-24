<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:35 PM
 */

namespace WTForms\Fields\HTML5;

use WTForms\Form;
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
   * @param array $options
   * @param Form  $form
   */
  public function __construct(array $options = [], Form $form = null)
  {
    parent::__construct($options, $form);
    $this->widget = new DateTimeInput();
  }
}
