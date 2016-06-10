<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 2:25 PM
 */

namespace WTForms\Fields\Core;

use WTForms\Exceptions\TypeError;
use WTForms\Exceptions\ValueError;
use WTForms\Form;
use WTForms\Widgets;
use WTForms\Widgets\Core\Option;
use WTForms\Widgets\Core\Select;

class SelectField extends SelectFieldBase
{
    /**
     * @var Option
     */
    public $option_widget;

    /**
     * @var array
     */
    public $choices = [];

    /**
     * @var callable|null
     */
    public $coerce;

    public function __construct(array $options = ['choices' => []])
    {
        if (array_key_exists('choices', $options)) {
            $this->choices = $options['choices'];
            unset($options['choices']);
        } else {
            $this->choices = [];
        }
        if (array_key_exists('coerce', $options)) {
            $this->coerce = $options['coerce'];
            unset($options['coerce']);
        } else {
            $this->coerce = function ($x) {
                return $x;
            };
        }
        $options = array_merge(["widget" => new Select()], $options);
        parent::__construct($options);
    }

    public function getChoices()
    {
        foreach ($this->choices as list($value, $label)) {
            yield [$value, $label, $value == $this->data];
        }
    }

    public function processData($value)
    {
        try {
            $this->data = $this->coerce->__invoke($value);
        } catch (ValueError $e) {
            $this->data = null;
        } catch (TypeError $e) {
            $this->data = null;
        }
    }

    public function processFormData(array $valuelist)
    {
        if ($valuelist) {
            try {
                $this->data = $this->coerce->__invoke($valuelist[0]);
            } catch (ValueError $e) {
                throw new ValueError("Invalid choice: could not coerce");
            }
        }
    }

    public function preValidate(Form $form)
    {
        foreach ($this->choices as list($v, $_)) {
            if ($this->data == $v) {
                return;
            }
        }

        throw new ValueError("Not a valid choice");
    }
}
