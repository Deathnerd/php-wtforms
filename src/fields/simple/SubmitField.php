<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 5:04 PM
 */

namespace Deathnerd\WTForms\Fields\Simple;
use Deathnerd\WTForms\Fields\Core\BooleanField;
use Deathnerd\WTForms\Widgets\Core\SubmitInput;

/**
 * Represents an ``<input type="submit">``.  This allows checking if a given
 * submit button has been pressed.
 * @package Deathnerd\WTForms\Fields\Simple
 */
class SubmitField extends BooleanField
{
    /**
     * @inheritdoc
     */
    public function __construct($label = "", array $kwargs = [])
    {
        parent::__construct($label, $kwargs);
        $this->widget = new SubmitInput();
    }

}
