<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 3:14 PM
 */

namespace WTForms\Fields\HTML5;

use WTForms\Widgets\HTML5\DateInput;

/**
 * Represents an ``<input type="date">``.
 * @package WTForms\Fields\HTML5
 */
class DateField extends \WTForms\Fields\Core\DateField
{
    /**
     * @inheritdoc
     */
    public function __construct(array $options = [])
    {
        $options = array_merge(["widget" => new DateInput()], $options);
        parent::__construct($options);
    }
}
