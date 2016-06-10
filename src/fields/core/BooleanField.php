<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 8:59 PM
 */

namespace WTForms\Fields\Core;

use WTForms\Widgets\Core\CheckboxInput;

/**
 * Represents an ``<input type="checkbox">``. Set the ``checked``-status by using the
 * ``default``-option. Any value for ``default``, e.g. ``default="checked"`` puts
 * ``checked`` into the html-element and sets the ``data`` to ``true``
 *
 * @package WTForms\Fields
 */
class BooleanField extends Field
{
    /**
     * @var array A sequence of strings to be considered "false" values for the field
     */
    public $false_values = ['false', ''];

    /**
     * @var boolean
     */
    public $data;

    /**
     * @inheritdoc
     */
    public function __construct(array $options = [])
    {
        $options = array_merge(["widget" => new CheckboxInput()], $options);
        if (array_key_exists("false_values", $options) && is_array($options['false_values'])) {
            $this->false_values = $options['false_values'];
            unset($options['false_values']);
        }
        parent::__construct($options);
    }

    /**
     * Process the data applied to this field and store the result.
     *
     * This will be called during form construction by the form's `options` or
     * `obj` argument.
     *
     * @param string|array $value
     */
    public function processData($value)
    {
        $this->data = boolval($value);
    }

    /**
     * @inheritdoc
     */
    public function processFormData(array $valuelist)
    {
        if (!$valuelist || in_array($valuelist[0], $this->false_values)) {
            $this->data = false;
        } else {
            $this->data = true;
        }
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if (in_array($name, ['value'])) {
            if ($this->raw_data !== null && !empty($this->raw_data)) {
                return strval($this->raw_data[0]);
            }

            return 'y';
        }

        return null;
    }
}
