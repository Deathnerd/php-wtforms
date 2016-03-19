<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/20/2016
 * Time: 10:10 PM
 */

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Render a basic ``<input>`` field
 *
 * This is used as the basis for most other input fields.
 *
 * By default, the `_value()` method will be called upon teh associated field
 * to provide the ``value=`` HTML attribute
 * @package WTForms\Widgets
 */
class Input extends Widget
{
  /**
   * @var string
   */
  public $input_type;

  /**
   * @param string $input_type If passed, will add ``type="$input_type"`` to the
   *                           html attributes for this input
   */
  public function __construct($input_type = "")
  {
    if ($input_type !== "") {
      $this->input_type = $input_type;
    }
  }

  /**
   * @param Field $field
   * @param array $kwargs
   *
   * @return string
   */
  public function __invoke(Field $field, array $kwargs = [])
  {
    $kwargs = (new OptionsResolver())->setDefaults([
        "id"    => $field->id,
        "type"  => $this->input_type,
        "value" => $field->_value(),
    ])->resolve($kwargs);
    $kwargs['name'] = $field->name;

    return sprintf("<input %s>", html_params($kwargs));
  }
}
