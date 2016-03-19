<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 11:07 AM
 */

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Render a single radio button.
 *
 * This widget is most commonly used in conjunction with ListWidget or some
 * other listing, as singular radio buttons are not very useful.
 * @package WTForms\Widgets\Core
 */
class RadioInput extends Input
{
  /**
   * RadioInput constructor.
   */
  public function __construct()
  {
    parent::__construct("radio");
  }

  /**
   * @param Field $field
   * @param array $kwargs
   *
   * @return string
   */
  public function __invoke(Field $field, array $kwargs = [])
  {
    if ($field->checked) {
      $kwargs['checked'] = true;
    }

    return parent::__invoke($field, $kwargs);
  }
}
