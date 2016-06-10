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
    public function __construct(array $options = ["step" => null])
    {
        $options = array_merge(["step" => null], $options);
        $this->step = $options['step'];
        parent::__construct("range");
    }

    /**
     * @param Field $field
     * @param array $options
     *
     * @return string
     */
    public function __invoke($field, array $options = [])
    {
        return parent::__invoke($field, array_merge(["step" => $this->step], $options));
    }
}
