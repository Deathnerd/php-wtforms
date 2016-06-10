<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 1:40 PM
 */

namespace WTForms\Fields\Core;

use WTForms\Exceptions\ValueError;
use WTForms\Widgets\Core\TextInput;

class FloatField extends Field
{
    /**
     * FloatField constructor.
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
                $this->data = floatval($valuelist[0]);
            } else {
                $this->data = null;
                throw new ValueError("Not a valid float value");
            }
        }
    }
}
