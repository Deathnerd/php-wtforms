<?php

declare(strict_types=1);

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Renders a list of fields as a set of table rows with th/td pairs.
 *
 * If `$with_table_tag` is true, then an enclosing `<table>` is placed around the
 * rows.
 *
 * Hidden fields will not be displayed with a row, instead the field will be
 * pushed into a subsequent table row to ensure XHTML validity. Hidden fields
 * at the end of the field list will appear outside the table.
 *
 * @extends Widget<mixed>
 */
class TableWidget extends Widget
{
  /**
   * If true, an enclosing `<table>` is placed around the rows.
   */
  public bool $with_table_tag;

  public function __construct(bool $with_table_tag = true)
  {
    $this->with_table_tag = $with_table_tag;
  }

  public function __invoke(Field $field, array $options = []): string
  {
    $html = '';
    $hidden = '';

    if ($this->with_table_tag) {
      $html .= sprintf('<table %s>', html_params(array_merge(['id' => $field->id], $options)));
    }

    foreach ($field as $subfield) {
      if (in_array($subfield->type, ['HiddenField', 'CSRFTokenField'])) {
        $hidden .= (string) $subfield;
      } else {
        $html .= sprintf('<tr><th>%s</th><td>%s%s</td></tr>', $subfield->label, $hidden, $subfield);
        $hidden = '';
      }
    }

    if ($this->with_table_tag) {
      $html .= '</table>';
    }

    if ($hidden) {
      $html .= $hidden;
    }

    return $html;
  }
}
