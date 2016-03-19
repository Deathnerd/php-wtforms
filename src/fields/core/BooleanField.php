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
     * Field constructor.
     * @param string $label
     * @param array $kwargs In addition to {@link Field}'s kwargs, you may
     * also pass an entry with key ``'false_values'`` which is a sequence of
     * strings of what is considered a "false" value. Defaults are ``['false', '']``
     * @throws \TypeError
     */
    public function __construct($label = "", array $kwargs = [])
    {
        parent::__construct($label, $kwargs);
        $this->widget = new CheckboxInput();
        if (array_key_exists("false_values", $kwargs) && is_array($kwargs['false_values'])) {
            $this->false_values = $kwargs['false_values'];
        }
    }

    /**
     * Process the data applied to this field and store the result.
     *
     * This will be called during form construction by the form's `kwargs` or
     * `obj` argument.
     * @param string|array $value
     */
    public function process_data($value)
    {
        $this->data = boolval($value);
    }

    /**
     * @inheritdoc
     */
    public function process_formdata(array $valuelist)
    {
        if (!$valuelist || in_array($valuelist[0], $this->false_values)) {
            $this->data = false;
        } else {
            $this->data = true;
        }
    }

    public function _value()
    {
        if ($this->raw_data !== null && !empty($this->raw_data)) {
            return strval($this->raw_data[0]);
        }
        return 'y';
    }
}
