<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/3/2016
 * Time: 4:57 PM
 */

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Renders a multi-line text area.
 *
 * `rows` and `cols` ought to be passed as members of the `$kwargs` array when
 * rendering
 * @package WTForms\Widgets\Core
 */
class TextArea extends Widget
{
  /**
   * @param Field $field
   * @param array $kwargs
   *
   * @return string
   */
  public function __invoke(Field $field, array $kwargs = [])
  {
    $kwargs = (new OptionsResolver())->setDefault("id", $field->id)->resolve($kwargs);
    $kwargs['name'] = $field->name;

    return sprintf("<textarea %s>%s</textarea>", html_params($kwargs), e($field->_value()));
  }
}