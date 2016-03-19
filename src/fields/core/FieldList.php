<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/6/2016
 * Time: 4:55 PM
 */

namespace WTForms\Fields\Core;

/**
 * Encapsulate an ordered list of multiple instances of the same field type,
 * keeping data as a list.
 *
 * >>> $authors = new FieldList(new StringField("name", [new DataRequired()]));
 * @package WTForms\Fields\Core
 */
class FieldList extends Field
{
  /**
   * Field constructor.
   *
   * @param string $label
   * @param Field  $field
   * @param array  $kwargs
   *
   * @throws \TypeError
   * @deprecated Not finished yet
   */
  public function __construct($label, Field $field, array $kwargs = [])
  {
    parent::__construct($label, $kwargs);
    if ($this->filters) {
      throw new \TypeError("FieldList does not accept any filters. Instead, define them on the enclosed field");
    }
  }
}