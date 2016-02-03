<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 3:22 PM
 */

namespace Deathnerd\WTForms\Fields\HTML5;

use Deathnerd\WTForms\Fields\Core\DateTimeField;
use Deathnerd\WTForms\Widgets\HTML5\DateTimeLocalInput;

/**
 * Represents an ``<input type="datetime-local">``.
 * @package Deathnerd\WTForms\Fields\HTML5
 */
class DateTimeLocalField extends DateTimeField
{
    /**
     * @inheritdoc
     */
    public function __construct($label = "", array $kwargs = [])
    {
        parent::__construct($label, $kwargs);
        $this->widget = new DateTimeLocalInput();
    }

}
