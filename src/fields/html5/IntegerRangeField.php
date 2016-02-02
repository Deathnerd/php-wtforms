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

class IntegerRangeField extends IntegerField
{
    /**
     * IntegerRangeField constructor.
     * @param string $label
     * @param array $kwargs
     */
    public function __construct($label = "", array $kwargs = [])
    {
        parent::__construct($label, $kwargs);
        $this->widget = new RangeInput(["step" => "any"]);
    }
}
