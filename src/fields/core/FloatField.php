<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 1:40 PM
 */

namespace Deathnerd\WTForms\Fields\Core;


use Deathnerd\WTForms\ValueError;
use Deathnerd\WTForms\Widgets\TextInput;

class FloatField extends Field
{
    /**
     * FloatField constructor.
     * @param string $label
     * @param array $kwargs
     */
    public function __construct($label = "", array $kwargs = [])
    {
        parent::__construct($label, $kwargs);
        $this->widget = new TextInput();
    }

    public function _value()
    {
        if ($this->raw_data) {
            return $this->raw_data[0];
        } elseif ($this->data !== null) {
            return strval($this->data);
        }
        return "";
    }

    public function process_formdata(array $valuelist)
    {
        if(!empty($valuelist)){
            if(is_numeric($valuelist[0])){
                $this->data = floatval($valuelist[0]);
            } else {
                $this->data = null;
                throw new ValueError($this->gettext("Not a valid float value"));
            }
        }
    }
}
