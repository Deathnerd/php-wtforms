<?php

declare(strict_types=1);

namespace WTForms\Fields\Core;

use WTForms\Widgets\Core\Option;
use WTForms\Widgets\Core\Widget;

/**
 * Base class for fields which can be iterated to produce options.
 *
 * This isn't a field, but an abstract base class for fields which want to
 * provide this functionality.
 *
 * @template TData
 *
 * @extends Field<TData>
 *
 * @property array<int, _Option> $options
 */
abstract class SelectFieldBase extends Field implements \Iterator
{
  /**
   * Widget used to render individual options.
   *
   * @var Option|Widget
   */
  public $option_widget;

  /**
   * @param array<string, mixed> $options
   */
  public function __construct(array $options = [])
  {
    $options = array_merge(['option_widget' => new Option()], $options);
    $this->option_widget = $options['option_widget'];
    unset($options['option_widget']);
    parent::__construct($options);
  }

  /**
   * @return array<int, _Option>|mixed
   */
  public function __get(string $name)
  {
    if ('options' == $name) {
      $this->options = [];
      $opts = [
        'widget' => $this->option_widget,
        'name'   => $this->name,
        'form'   => $this->form,
        'meta'   => $this->meta,
      ];
      $i = 0;
      foreach ($this->getChoices() as [$value, $label, $selected]) {
        $opts['id'] = sprintf('%s-%s', $this->id, $i);
        $opts['label'] = $label;
        ++$i;
        $opt = new _Option($opts);
        $opt->process([], $value);
        $opt->checked = $selected;
        $this->options[] = $opt;
      }

      return $this->options;
    }

    return parent::__get($name); // @codeCoverageIgnore
  }

  /**
   * Provides data for choice widget rendering. Must return an array
   * of `[value,label,selected]` tuples.
   *
   * @return iterable<int, array{0: string|int, 1: string, 2: bool}>
   */
  abstract public function getChoices(): iterable;
}
