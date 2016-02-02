<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:25 PM
 */

namespace Deathnerd\WTForms\Fields\HTML5;

use Deathnerd\WTForms\Fields\Core\StringField;
use Deathnerd\WTForms\Widgets\HTML5\URLInput;

class URLField extends StringField
{
    /**
     * URLField constructor.
     * @param string $label
     * @param array $kwargs
     */
    public function __construct($label, array $kwargs)
    {
        parent::__construct($label, $kwargs);
        $this->widget = new URLInput();
    }
}
