<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 11:04 AM
 */

namespace WTForms\Fields\Core;

use WTForms\Widgets\Core\ListWidget;
use WTForms\Widgets\Core\RadioInput;

/**
 * Like a SelectField, except displays a list of radio buttons.
 *
 * Iterating a field will produce subfields (each containing a label as
 * well) in order to allow custom rendering of the individual radio fields.
 *
 * @package WTForms\Fields
 */
class RadioField extends SelectField
{
    /**
     * @inheritdoc
     */
    public function __construct(array $options = ['choices' => []])
    {
        $options = array_merge(["widget" => new ListWidget("ul", false), "option_widget" => new RadioInput()],
            $options);
        parent::__construct($options);
    }
}
