<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 2:10 PM
 */

namespace WTForms\Fields\Core;

use WTForms\NotImplemented;
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

  private $options = [];

  /**
   * SelectFieldBase constructor.
   *
   * @param string $label
   * @param array  $options
   */
  public function __construct($label = "", array $options = ['validators' => [], 'option_widget' => null])
  {
    $options = array_merge(['validators' => [], 'option_widget' => null], $options);
    parent::__construct($label, $options);
    $this->option_widget = $options['option_widget'] ?: new Option();
    if (is_string($this->option_widget)) {
      $w = $this->option_widget;
      $this->option_widget = new $w();
    }
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
      if ($this->options) {
        return $this->options;
      }
      $this->options = [];
      $opts = ['widget' => $this->option_widget, 'name' => $this->name, 'form' => $this->form, 'meta' => $this->meta];
      $options = $this->getChoices();
      for ($i = 0; $i < count($options); $i++) {
        $opts['id'] = "{$this->id}-{$i}";
        $opt = new _Option($options[$i]['label'], $opts);
        $opt->process([], $options[$i]['value']);
        $opt->checked = (array_key_exists('selected', $options[$i]) && $options[$i]['selected'])
            || (array_key_exists('checked', $options[$i]) && $options[$i]['checked']);
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
