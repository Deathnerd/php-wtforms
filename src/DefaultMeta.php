<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/31/2015
 * Time: 11:34 AM
 */

namespace WTForms;

use WTForms\CSRF\Session\SessionCSRF;
use WTForms\Fields;
use WTForms\Fields\Core\Field;

/**
 * This is the default Meta class which defines all the default values
 * and therefore is also the 'API' of the class Meta interface
 * @package WTForms
 */
class DefaultMeta
{
    /**
     * @var string
     */
    public $csrf_field_name = "csrf_token";
    /**
     * @var string
     */
    public $csrf_time_limit;
    /**
     * @var bool
     */
    public $csrf = false;
    /**
     * @var null
     */
    public $csrf_secret = null;
    /**
     * @var null|array|object
     */
    public $csrf_context = null;
    /**
     * @var null|callable
     */
    public $csrf_class = null;

    /**
     * @param Field $field
     * @param array $render_kw
     *
     * @return mixed
     */
    public function renderField(Field $field, $render_kw = [])
    {
        $other_kw = property_exists($field, 'render_kw') ? $field->render_kw : null;
        if (!is_null($other_kw)) {
            $render_kw = array_merge($other_kw, $render_kw);
        }
        $widget = $field->widget;

        return $widget($field, $render_kw);
    }

    /**
     * Build a CSRF implementation. This is called once per form instance
     * The default implementation builds the class referenced to by
     * {@link csrf_class} with zero arguments. If {@link csrf_class} is ``null``,
     * will instead use the default implementation {@link \WTForms\CSRF\Session\SessionCSRF}.
     *
     * @param Form $form The form
     *
     * @return object|SessionCSRF A CSRF representation
     */
    public function buildCSRF(Form $form)
    {
        if (!is_null($this->csrf_class)) {
            return (new \ReflectionClass($this->csrf_class))->newInstance();
        }

        return new SessionCSRF();
    }

    /**
     * Given an associative array of values, update values on this `Meta` instance.
     *
     * @param array $values
     */
    public function updateValues(array $values = [])
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }
}
