<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 2:10 PM
 */

namespace Deathnerd\WTForms\Fields;

use Deathnerd\WTForms\NotImplemented;
use Deathnerd\WTForms\Widgets\Option;

abstract class SelectFieldBase extends Field
{
    /**
     * @var \Deathnerd\WTForms\Widgets\Option
     */
    public $option_widget;

    /**
     * SelectFieldBase constructor.
     * @param string $label
     * @param array $validators
     * @param Option $option_widget
     * @param array $kwargs
     * @throws \TypeError
     */
    public function __construct($label = "", array $validators = [], Option $option_widget = null, array $kwargs = [])
    {
        $kwargs['validators'] = $validators;
        parent::__construct($label, $kwargs);
        if (!is_null($option_widget)) {
            $this->option_widget = $option_widget;
        } else {
            $this->option_widget = new Option();
        }
    }

    /**
     * Provides data for choice widget rendering. Must return a sequence or
     * iterable of `[value,label,selected]` tuples
     * @return array
     * @throws NotImplemented
     */
    public function iter_choices()
    {
        throw new NotImplemented();
    }

    //TODO Implement PHP equivalent of __iter__
}

//TODO Decide if Namespacing this to only be accessible from SelectFieldBase is necessary
class _Option extends Field
{
    public $checked = false;
}