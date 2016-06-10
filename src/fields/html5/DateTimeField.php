<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:35 PM
 */

namespace WTForms\Fields\HTML5;

use WTForms\Widgets\HTML5\DateTimeInput;

/**
 * Represents an ``<input type="datetime">``.
 * @package WTForms\Fields\HTML5
 */
class DateTimeField extends \WTForms\Fields\Core\DateTimeField
{
    /**
     * DateTimeField constructor.
     *
     * @param array $options
     *   $form
     */
    public function __construct(array $options = [])
    {
        $options = array_merge(["widget" => new DateTimeInput()], $options);
        parent::__construct($options);
    }
}
