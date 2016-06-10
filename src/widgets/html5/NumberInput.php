<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 3:48 PM
 */

namespace WTForms\Widgets\HTML5;

use WTForms\Widgets\Core\Input;

/**
 * Renders an input with type "number".
 * @package WTForms\Widgets\HTML5
 */
class NumberInput extends Input
{
    public $min;
    public $max;
    public $step;

    /**
     * @inheritdoc
     */
    public function __construct(array $options = ["step" => null, "min" => null, "max" => null])
    {
        $options = array_merge(["step" => null, "min" => null, "max" => null], $options);
        $this->step = $options['step'];
        $this->min = $options['min'];
        $this->max = $options['max'];
        parent::__construct("number");
    }

    /**
     * @inheritdoc
     */
    public function __invoke($field, array $options = [])
    {
        foreach (['step', 'min', 'max'] as $option) {
            if (!array_key_exists($option, $options) && $this->$option) {
                $options[$option] = $this->$option;
            }
        }

        return parent::__invoke($field, $options);
    }
}

