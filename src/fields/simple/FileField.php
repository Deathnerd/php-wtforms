<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 5:02 PM
 */

namespace WTForms\Fields\Simple;

use WTForms\Fields\Core\StringField;
use WTForms\Widgets\Core\FileInput;

/**
 * Can render a file-upload field.  Will take any passed filename value, if
 * any is sent by the browser in the post params.  This field will NOT
 * actually handle the file upload portion, as wtforms does not deal with
 * individual frameworks' file handling capabilities.
 * @package WTForms\Fields\Simple
 */
class FileField extends StringField
{
    /**
     * @inheritdoc
     */
    public function __construct(array $options = [])
    {
        $options = array_merge(["widget" => new FileInput()], $options);
        parent::__construct($options);
    }
}
