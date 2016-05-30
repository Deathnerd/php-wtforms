<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 2:10 PM
 */

namespace WTForms\Fields\Core;

use WTForms\Exceptions\NotImplemented;
use WTForms\Form;
use WTForms\Widgets\Core\Option;

/**
 * Base class for fields which can be iterated to produce options.
 *
 * This isn't a field, but an abstract base class for fields which want to
 * provide this functionality.
 *
 * @package WTForms\Fields\Core
 */
abstract class SelectFieldBase extends Field implements \Iterator
{
  /**
   * @var Option
   */
  public $option_widget;


  /**
   * SelectFieldBase constructor.
   *
   * @param Form  $form
   * @param array $options
   */
  public function __construct(array $options = [], Form $form = null)
  {
    $options = array_merge(['option_widget' => null], $options);
    $this->option_widget = $options['option_widget'] ?: new Option();
    unset($options['option_widget']);
    parent::__construct($options, $form);
  }

  /**
   * Provides data for choice widget rendering. Must return an array
   * of `[value,label,selected]` tuples
   * @return array
   * @throws NotImplemented
   */
  public function getChoices()
  {
    throw new NotImplemented();
  }

  public function __get($name)
  {
    if ($name == "options") {
      $this->options = [];
      $opts = ['widget' => $this->option_widget, 'name' => $this->name, 'form' => $this->form, 'meta' => $this->meta];
      $i = 0;
      foreach ($this->getChoices() as list($value, $label, $selected)) {
        $opts['id'] = "{$this->id}-{$i}";
        $opts['label'] = $label;
        $i++;
        $opt = new _Option($opts);
        $opt->process([], $value);
        $opt->checked = $selected;
        $this->options[] = $opt;
      }

      return $this->options;
    }

    return parent::__get($name);
  }
}

class _Option extends Field
{
  public $checked = false;

  public function __get($name)
  {
    return $this->data;
  }
}
