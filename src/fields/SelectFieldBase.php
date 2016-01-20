<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 2:10 PM
 */

namespace Deathnerd\WTForms\Fields;

abstract class SelectFieldBase extends Field
{
    /**
     * @var \Widgets\Option
     */
    public $option_widget;

    /**
     * SelectFieldBase constructor.
     * @param string $label
     * @param array $validators
     * @param \Widgets\Option $option_widget
     * @param array $kwargs
     * @throws \TypeError
     */
    public function __construct($label = "", array $validators = [], \Widgets\Option $option_widget = null, array $kwargs = [])
    {
        $kwargs['validators'] = $validators;
        parent::__construct($label, $kwargs);
        $this->option_widget = new Option();

        if(!is_null($option_widget)){
            $this->option_widget = $option_widget;
        }
    }

    /**
     * Provides data for choice widget rendering. Must return a sequence or
     * iterable of `[value,label,selected]` tuples
     * @return array
     */
    public function iter_choices(){
        throw new \RuntimeException;
    }

    //TODO Implement PHP equivalent of __iter__
}

class _Option extends Field{
    public $checked = false;


}