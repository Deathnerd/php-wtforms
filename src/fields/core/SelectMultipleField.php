<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 11:15 AM
 */

namespace WTForms\Fields\Core;


use WTForms\Exceptions\TypeError;
use WTForms\Exceptions\ValueError;
use WTForms\Form;
use WTForms\Widgets\Core\Select;

/**
 * No different from a normal select field, except this one can take (and
 * validate) multiple choices. You'll need to specify the HTML `size`
 * attribute to the select field when rendering
 *
 * @package WTForms\Fields
 */
class SelectMultipleField extends SelectField
{
    /**
     * @var array
     */
    public $data;

    public function __construct(array $options = ['choices' => []])
    {
        $options = array_merge(["widget" => new Select()], $options);
        parent::__construct($options);
        $this->widget->multiple = true;
    }

    /**
     * Provides data for choice widget rendering. Must return a sequence or
     * iterable of `[value,label,selected]` tuples
     * @return \Generator
     */
    public function getChoices()
    {
        foreach ($this->choices as list($value, $label)) {
            yield [$value, $label, $this->data !== null && in_array($value, $this->data)];
        }
    }

    /**
     * @param array|null $value
     */
    public function processData($value)
    {
        try {
            if (!is_array($value)) {
                throw new TypeError;
            }
            $d = [];
            foreach ($value as $v) {
                $d[] = $this->coerce->__invoke($v);
            }
        } catch (ValueError $e) {
            $d = null;
        } catch (TypeError $e) {
            $d = null;
        }
        $this->data = $d;
    }

    public function processFormData(array $valuelist)
    {
        try {
            $d = [];
            foreach ($valuelist as $v) {
                $d[] = $this->coerce->__invoke($v);
            }
        } catch (ValueError $e) {
            throw new ValueError('Invalid choice(s): one or more data inputs could not be coerced');
        }
        $this->data = $d;
    }

    public function preValidate(Form $form)
    {
        if ($this->data) {
            $values = [];
            foreach ($this->choices as $c) {
                $values[] = $c[0];
            }
            foreach ($this->data as $d) {
                if (!in_array($d, $values)) {
                    throw new ValueError("'$d' is not a valid choice for this field");
                }
            }
        }
    }
}
