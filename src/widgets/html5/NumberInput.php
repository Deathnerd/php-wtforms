<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 3:48 PM
 */

namespace Deathnerd\WTForms\Widgets\HTML5;
use Deathnerd\WTForms\Fields\Core\Field;
use Deathnerd\WTForms\Widgets\Core\Input;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Renders an input with type "number".
 * @package Deathnerd\WTForms\Widgets\HTML5
 */
class NumberInput extends Input
{
    public $min;
    public $max;
    public $step;

    /**
     * @inheritdoc
     */
    public function __construct(array $kwargs = [])
    {
        $kwargs = (new OptionsResolver())->setDefaults([
            "step" => null,
            "min" => null,
            "max" => null,
        ])->resolve($kwargs);
        $this->step = $kwargs['step'];
        $this->min = $kwargs['min'];
        $this->max = $kwargs['max'];
        parent::__construct("number");
    }

    /**
     * @inheritdoc
     */
    public function __invoke(Field $field, array $kwargs = [])
    {
        $kwargs = (new OptionsResolver())->setDefaults([
            "step" => $this->step,
            "min" => $this->min,
            "max" => $this->max
        ])->resolve($kwargs);
        return parent::__invoke($field, $kwargs);
    }

}
