<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/29/2016
 * Time: 8:09 PM
 */

namespace Deathnerd\WTForms;

use Deathnerd\WTForms\Fields\Core\Field;
use Deathnerd\WTForms\Fields\Core\UnboundField;
use Deathnerd\WTForms\Utils\Meta;

/**
 * The metaclass for `Form` and any subclasses of `Form`.
 *
 * `FormMeta`'s responsibility is to create the `_unbound_fields` list, which
 * is a list of `UnboundField` instances sorted by their order of
 * instantiation.  The list is created at the first instantiation of the form.
 * If any fields are added/removed from the form, the list is cleared to be
 * re-generated on the next instantiation.
 *
 * Any properties which begin with an underscore or are not `UnboundField`
 * instances are ignored by the metaclass.
 * @package Deathnerd\WTForms
 * @property $Meta Meta
 */
class FormMeta extends BaseForm
{
    public $_unbound_fields;
    public $_wtforms_meta;

    /**
     * FormMeta constructor.
     * @param array $fields
     * @param string $prefix
     * @param DefaultMeta|null $meta
     */
    // TODO Figure out constructor order
    public function __construct($name = "", $bases = [], $d)
    {
        $this->_create_unbound_fields($name);
        $this->_create_metas(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT));
        parent::__construct($fields, $prefix, $meta);
    }

    /**
     * @param string $field_name
     * @param Field|UnboundField $value
     */
    public function __set($field_name, $value)
    {
        if ($field_name == "Meta") {
            $this->_wtforms_meta = null;
        } elseif (!starts_with($field_name, "_") && property_exists($value, "_formfield")) {
            $this->_unbound_fields = null;
        }
        parent::__set($field_name, $value);
    }

    private function _create_unbound_fields($name)
    {
        $fields = [];
        foreach (get_object_vars($this) as $var_name => $var) {
            if (!starts_with($var_name, "_")) {
                $unbound_field = $var;
                if (is_object($unbound_field) && property_exists($unbound_field, "_formfield")) {
                    $fields[] = [$var_name, $unbound_field];
                }
            }
        }
        sort($fields);
        $this->_unbound_fields = $fields;
    }

    private function _create_metas(array $backtrace)
    {
        $bases = [];
        foreach ($backtrace as $b) {
            /** @var $b Form|FormMeta|BaseForm */
            if ($b instanceof Form || $b instanceof FormMeta || $b instanceof BaseForm) {
                if (property_exists($b, "Meta")) {
                    $bases[] = $b->Meta;
                }
            }
        }
        $this->_wtforms_meta = new Meta($bases);
    }

    /**
     * @param string $field_name
     */
    public function __unset($field_name)
    {
        if (!starts_with($field_name, "_")) {
            $this->_unbound_fields = null;
        }
        parent::__unset($field_name);
    }
}
