<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/31/2015
 * Time: 11:34 AM
 */

namespace Deathnerd\WTForms;

use Deathnerd\WTForms\Fields;
use Deathnerd\WTForms\Fields\Core\Field;
use Deathnerd\WTForms\Fields\Core\UnboundField;
use Deathnerd\WTForms\Widgets\Core\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * This is the default Meta class which defines all the default values
 * and therefore is also the 'API' of the class Meta interface
 * @package Deathnerd\WTForms
 */
class DefaultMeta
{
    /**
     * @var string
     */
    public $csrf_field_name = "csrf_token";
    /**
     * @var bool
     */
    public $csrf = false;
    /**
     * @var null
     */
    public $csrf_secret = null;
    /**
     * @var null
     */
    public $csrf_context = null;
    /**
     * @var null|callable
     */
    public $csrf_class = null;
    public $locales = false;
    public $cache_translations = true;
    public $translations_cache = [];

    public function bind_field(BaseForm $form, UnboundField $unbound_field, array $options)
    {
        // TODO Finish DefaultMeta
        $prefix = array_key_exists($options, 'prefix') ? $options['prefix'] : "";
        $translations = array_key_exists($options, 'prefix') ? $options['translations'] : null;
        return $unbound_field->bind($form, $options['name'], $prefix, $translations);
    }

    /**
     * `wrap_formdata` allows doing custom wrappers of WTForms formdata.
     *
     * Unlike the original Python implementation, PHP doesn't have to use
     * WSGI middleware to interact with the HTTP request and can instead access
     * POST,GET, and other HTTP Response data directly. The original Python
     * implemented a Webob wrapper, which is a PEP spec interface.
     *
     * This implementation instead is for interfacing with other frameworks
     * and libraries such as Illuminate's Collection and (in the future)
     * Laravel and Symfony type HTTPResponse objects.
     *
     * It takes in an iterable object and converts them to plain PHP arrays.
     *
     * Override if you need to implement your own wrapper.
     * TODO: Accept Symfony HTTP Response objects
     * @param Form $form
     * @param array|Collection $formdata
     * @return array
     */
    public function wrap_formdata(Form $form, $formdata)
    {
        if ($formdata instanceof Collection || $formdata instanceof Request) {
            return $formdata->all();
        }
        return $formdata;
    }

    /**
     * @param Field $field
     * @param array $render_kw
     * @return mixed
     */
    public function render_field(Field $field, $render_kw = [])
    {
        $other_kw = property_exists($field, 'render_kw') ? $field->render_kw : null;
        if (!is_null($other_kw)) {
            $render_kw = array_merge($other_kw, $render_kw);
        }
        /** @var Widget $widget */
        $widget = $field->widget;

        return $widget($field, $render_kw);
    }


    /**
     * Override in subclasses to provide alternate translations factory.
     *
     * @return object An obejct that provides gettext() and ngettext() methods
     */
    public function get_translations()
    {
        $locales = $this->locales;
        if ($locales === false) {
            return null;
        }

        $locales = ($locales != false) ? $locales : null;
        if (is_array($locales)) {
            $locales = $locales[0];
        }

        if ($this->cache_translations === true) {
            $translations = $this->translations_cache[$locales];

            if ($translations === null) {
                $this->translations_cache[$locales] = get_translations($locales);
                $translations = $this->translations_cache[$locales];
            }

            return $translations;
        }

        return get_translations($locales);
    }
}
