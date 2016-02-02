<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 4:30 PM
 */

namespace Deathnerd\WTForms\Fields\HTML5;

use Deathnerd\WTForms\Fields\Core\IntegerField;
use Deathnerd\WTForms\Widgets\HTML5\RangeInput;

/**
 * Represents an ``<input type="range">``.
 * @package Deathnerd\WTForms\Fields\HTML5
 */
class IntegerRangeField extends IntegerField
{
    /**
     * @inheritdoc
     */
    public function __construct($label = "", array $kwargs = [])
    {
        parent::__construct($label, $kwargs);
        $this->widget = new RangeInput(["step" => "1"]);
    }
}
