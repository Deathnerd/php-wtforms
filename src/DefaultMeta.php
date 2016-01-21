<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/31/2015
 * Time: 11:34 AM
 */

namespace Deathnerd\WTForms;


use Deathnerd\WTForms\Fields\Field;
use Deathnerd\WTForms\Fields\UnboundField;
use Illuminate\Support\Collection;
use Deathnerd\WTForms\Fields;

/**
 * This is the default Meta class which defines all the default values
 * and therefore is also the 'API' of the class Meta interface
 * @package Deathnerd\WTForms
 */
class DefaultMeta
{
    public function bind_field(Form $form, UnboundField $unbound_field, Collection $options)
    {
        // TODO Finish DefaultMeta
        return $unbound_field->bind();
    }

    /**
     * @param Field $field
     * @param array $render_kw
     * @return mixed
     */
    public function render_field(Field $field, $render_kw = [])
    {
        $other_kw = property_exists($field, 'render_kw') ? $field->render_kw : null;
        if(!is_null($other_kw)){
            $render_kw = array_merge($other_kw, $render_kw);
        }
        return $field->widget->call($field, $render_kw);
    }
}