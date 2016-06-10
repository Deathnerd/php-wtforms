<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 4:05 PM
 */

namespace WTForms\Fields\HTML5;

use WTForms\Widgets\HTML5\NumberInput;

/**
 * Represents an ``<input type="number">``.
 * @package WTForms\Fields\HTML5
 */
class DecimalField extends \WTForms\Fields\Core\DecimalField
{
    /**
     * @inheritdoc
     */
    public function __construct(array $options = [])
    {
        $options = array_merge(["widget" => new NumberInput(["step" => '1'])], $options);
        parent::__construct($options);
    }

}
