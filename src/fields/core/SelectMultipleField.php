<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 11:15 AM
 */

namespace Deathnerd\WTForms\Fields;


use Deathnerd\WTForms\Form;
use Deathnerd\WTForms\ValueError;
use Deathnerd\WTForms\Widgets\Select;

/**
 * No different from a normal select field, except this one can take (and
 * validate) multiple choices. You'll need to specify the HTML `size`
 * attribute to the select field when rendering
 *
 * @package Deathnerd\WTForms\Fields
 */
class SelectMultipleField extends SelectField
{
    /**
     * @var array
     */
    public $data;

    public function __construct($label = "", array $validators = [], array $choices = [], array $kwargs = [])
    {
        parent::__construct($label, $validators, $choices, $kwargs);
        $this->widget = new Select(true);
    }

    /**
     * Provides data for choice widget rendering. Must return a sequence or
     * iterable of `[value,label,selected]` tuples
     * @return \Generator
     */
    public function iter_choices()
    {
        foreach ($this->choices as $value => $label) {
            $selected = $this->data !== null && in_array($value, $this->data);
            yield [$value, $label, $selected];
        }
    }

    /**
     * @param array|null $value
     */
    public function process_data(array $value = null)
    {
        foreach ($value as $v) {
            $this->data[] = strval($v);
        }
    }

    public function process_formdata(array $valuelist = [])
    {
        foreach ($valuelist as $v) {
            $this->data[] = strval($v);
        }
    }

    public function pre_validate(Form $form)
    {
        if ($this->data !== null) {
            $values = [];
            foreach ($this->choices as $c) {
                $values[] = $c[0];
            }
            foreach ($this->data as $d) {
                if (!in_array($d, $values)) {
                    throw new ValueError($this->gettext(sprintf("'%s' is not a valid choice for this field", $d)));
                }
            }
        }
    }
}