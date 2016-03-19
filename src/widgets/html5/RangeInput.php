<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 5:23 PM
 */

namespace WTForms\Widgets\HTML5;

use WTForms\Fields\Core\Field;
use WTForms\Widgets\Core\Input;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Renders an input with the type "range".
 * @package WTForms\Widgets\HTML5
 */
class RangeInput extends Input
{
  public $step;

  /**
   * @inheritdoc
   */
  public function __construct(array $kwargs = [])
  {
    $kwargs = (new OptionsResolver())->setDefault('step', null)->resolve($kwargs);
    $this->step = $kwargs['step'];
    parent::__construct("month");
  }

  /**
   * @param Field $field
   * @param array $kwargs
   *
   * @return string
   */
  public function __invoke(Field $field, array $kwargs = [])
  {
    $kwargs = (new OptionsResolver())->setDefault("step", $this->step)->resolve($kwargs);

    return parent::__invoke($field, $kwargs);
  }
}
