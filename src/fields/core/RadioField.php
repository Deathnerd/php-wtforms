<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 11:04 AM
 */

namespace Deathnerd\WTForms\Fields\Core;

use Deathnerd\WTForms\Widgets\Core\ListWidget;
use Deathnerd\WTForms\Widgets\Core\RadioInput;

/**
 * Like a SelectField, except displays a list of radio buttons.
 *
 * Iterating a field will produce subfields (each containing a label as
 * well) in order to allow custom rendering of the individual radio fields.
 *
 * @package Deathnerd\WTForms\Fields
 */
class RadioField extends SelectField
{
    /**
     * @inheritdoc
     */
    public function __construct($label = "", array $validators = [], array $choices = [], array $kwargs = [])
    {
        parent::__construct($label, $validators, $choices, $kwargs);
        $this->option_widget = new RadioInput();
        $this->widget = new ListWidget("ul", false);
    }
}
