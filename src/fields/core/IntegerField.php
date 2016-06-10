<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 1:24 PM
 */

namespace WTForms\Fields\Core;

use WTForms\Exceptions\ValueError;
use WTForms\Widgets\Core\TextInput;

/**
 * A text field, except all input is coerced to an integer. Erroneous input
 * is ignored and will nto be accepted as a value
 *
 * @package WTForms\Fields
 */
class IntegerField extends Field
{
    /**
     * @var TextInput
     */
    public $widget;

    /**
     * IntegerField constructor.
     *
     *   $form
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $options = array_merge(["widget" => new TextInput()], $options);
        parent::__construct($options);
    }

    public function processFormData(array $valuelist)
    {
        if ($valuelist) {
            if (is_numeric($valuelist[0])) {
                $this->data = intval($valuelist[0]);
            } else {
                $this->data = null;
                throw new ValueError("Not a valid integer value");
            }
        }
    }
}
