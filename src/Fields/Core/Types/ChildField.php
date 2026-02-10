<?php

namespace WTForms\Fields\Core\Types;

use WTForms\Fields\Core\Field;
use WTForms\Fields\Options\FieldOptions;

/**
 * @template InnerFieldDataType
 * @template Paren of Field<InnerFieldDataType>
 * @extends Field<InnerFieldDataType>
 */
class InnerField extends Field
{


  public function _construct(FieldOptions $options = new FieldOptions()) {
    parent::__construct($options);
  }

}