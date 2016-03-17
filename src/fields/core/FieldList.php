<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/6/2016
 * Time: 4:55 PM
 */

namespace Deathnerd\WTForms\Fields\Core;

/**
 * Encapsulate an ordered list of multiple instances of the same field type,
 * keeping data as a list.
 *
 * >>> $authors = new FieldList(new StringField("name", [new DataRequired()]));
 * @package Deathnerd\WTForms\Fields\Core
 */
class FieldList extends Field
{
    /**
     * Field constructor.
     * @param UnboundField $unbound_field
     * @param string $label
     * @param array $kwargs
     * @throws \TypeError
     */
    public function __construct(UnboundField $unbound_field, $label = "", array $kwargs = [])
    {
        parent::__construct($label, $kwargs);
        if ($this->filters) {
            throw new \TypeError("FieldList does not accept any filters. Instead, define them on the enclosed field");
        }
        assert($unbound_field instanceof UnboundField, "Field must be unbound, not a field class");
}