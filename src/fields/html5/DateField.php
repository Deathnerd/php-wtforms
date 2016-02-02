<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 3:14 PM
 */

namespace Deathnerd\WTForms\Fields\HTML5;

use Deathnerd\WTForms\Widgets\HTML5\DateInput;
/**
 * Represents an ``<input type="date">``.
 * @package Deathnerd\WTForms\Fields\HTML5
 */
class DateField extends \Deathnerd\WTForms\Fields\Core\DateField
{
    /**
     * @inheritdoc
     */
    public function __construct($label, array $kwargs)
    {
        parent::__construct($label, $kwargs);
        $this->widget = new DateInput();
    }
}
