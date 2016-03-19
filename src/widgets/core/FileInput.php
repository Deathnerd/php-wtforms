<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/3/2016
 * Time: 3:31 PM
 */

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Renders a file input chooser field.
 *
 * @package WTForms\Widgets\Core
 */
class FileInput extends Widget
{
  /**
   * @param Field $field
   * @param array $kwargs
   *
   * @return string
   */
  public function __invoke(Field $field, array $kwargs = [])
  {
    $kwargs = (new OptionsResolver())->setDefault('id', $field->id)->resolve($kwargs);
    $kwargs['name'] = $field->name;
    $kwargs['type'] = "file";

    return sprintf("<input %s>", html_params($kwargs));
  }

}