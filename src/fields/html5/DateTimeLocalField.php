<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 3:22 PM
 */

namespace WTForms\Fields\HTML5;

use WTForms\Widgets\HTML5\DateTimeLocalInput;

/**
 * Represents an ``<input type="datetime-local">``.
 * @package WTForms\Fields\HTML5
 */
class DateTimeLocalField extends DateTimeField
{
    /**
     * @inheritdoc
     */
    public function __construct(array $options = [])
    {
        $options = array_merge(["widget" => new DateTimeLocalInput()], $options);
        parent::__construct($options);
    }

}
