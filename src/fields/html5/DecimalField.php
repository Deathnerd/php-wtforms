<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 4:05 PM
 */

namespace Deathnerd\WTForms\Fields\HTML5;

use Deathnerd\WTForms\Widgets\HTML5\NumberInput;

/**
 * Represents an ``<input type="number">``.
 * @package Deathnerd\WTForms\Fields\HTML5
 */
class DecimalField extends \Deathnerd\WTForms\Fields\Core\DecimalField
{
    /**
     * @inheritdoc
     */
    public function __construct($label = "", array $kwargs = [])
    {
        parent::__construct($label, $kwargs);
        $this->widget = new NumberInput(["step" => '1']);
    }

}
