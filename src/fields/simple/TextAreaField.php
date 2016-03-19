<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 4:49 PM
 */

namespace WTForms\Fields\Simple;

use WTForms\Fields\Core\StringField;
use WTForms\Widgets\Core\TextArea;

/**
 * This field represents an HTML ``<textarea>`` and can be used to take
 * multi-line input.
 * @package WTForms\Fields\Simple
 */
class TextAreaField extends StringField
{
    /**
     * @inheritdoc
     */
    public function __construct($label = "", array $kwargs = [])
    {
        parent::__construct($label, $kwargs);
        $this->widget = new TextArea();
    }

}
