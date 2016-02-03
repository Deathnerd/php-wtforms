<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 2:10 PM
 */

namespace Deathnerd\WTForms\Fields\Core;

use Deathnerd\WTForms\NotImplemented;
use Deathnerd\WTForms\Widgets\Core\Option;

/**
 * Base class for fields which can be iterated to produce options.
 *
 * This isn't a field, but an abstract base class for fields which want to
 * provide this functionality.
 *
 * @package Deathnerd\WTForms\Fields\Core
 */
abstract class SelectFieldBase extends Field
{
    /**
     * @var Option
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
        $this->option_widget = $option_widget ?: new Option();
    }

    /**
     * Provides data for choice widget rendering. Must return a sequence or
     * iterable of `[value,label,selected]` tuples
     * @return \Generator
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
