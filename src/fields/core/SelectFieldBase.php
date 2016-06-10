<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 2:10 PM
 */

namespace WTForms\Fields\Core;

use WTForms\Exceptions\NotImplemented;
use WTForms\Widgets\Core\Option;

/**
 * Base class for fields which can be iterated to produce options.
 *
 * This isn't a field, but an abstract base class for fields which want to
 * provide this functionality.
 *
 * @property array $options
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
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $options = array_merge(['option_widget' => new Option()], $options);
        $this->option_widget = $options['option_widget'];
        unset($options['option_widget']);
        parent::__construct($options);
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if ($name == "options") {
            $this->options = [];
            $opts = [
                'widget' => $this->option_widget,
                'name'   => $this->name,
                'form'   => $this->form,
                'meta'   => $this->meta
            ];
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

        return parent::__get($name); // @codeCoverageIgnore
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
}

class _Option extends Field
{
    public $checked = false;

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if (is_bool($this->data)) {
            return $this->data ? "true" : "false";
        }

        return strval($this->data);
    }
}
