<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:27 PM
 */

namespace WTForms\Fields\HTML5;

use WTForms\Fields\Core\StringField;
use WTForms\Form;
use WTForms\Widgets\HTML5\EmailInput;

/**
 * Represents an ``<input type="email">``.
 * @package WTForms\Fields\HTML5
 */
class EmailField extends StringField
{
  /**
   * @inheritdoc
   */
  public function __construct(array $options = [], Form $form = null)
  {
    $options = array_merge(["widget" => new EmailInput()], $options);
    parent::__construct($options, $form);
  }
}
