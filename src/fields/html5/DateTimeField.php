<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:35 PM
 */

namespace Deathnerd\WTForms\Fields\HTML5;
use Deathnerd\WTForms\Widgets\HTML5\DateTimeInput;

class DateTimeField extends \Deathnerd\WTForms\Fields\Core\DateTimeField
{
    /**
     * DateTimeField constructor.
     * @param string $label
     * @param array $kwargs
     */
    public function __construct($label, array $kwargs)
    {
        parent::__construct($label, $kwargs);
        $this->widget = new DateTimeInput();
    }
}
