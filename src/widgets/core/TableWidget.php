<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/3/2016
 * Time: 4:17 PM
 */

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
 * @package WTForms\Widgets\Core
 */
class TableWidget extends Widget
{
    /**
     * @var bool If true, an enclosing `<table>` is placed around the rows.
     */
    public $with_table_tag;

    /**
     * TableWidget constructor.
     *
     * @param bool $with_table_tag
     */
    public function __construct($with_table_tag = true)
    {
        $this->with_table_tag = $with_table_tag;
    }

    /**
     * @param Field $field
     * @param array $options
     *
     * @return string
     */
    public function __invoke($field, array $options = [])
    {
        $html = "";
        $hidden = "";

        if ($this->with_table_tag) {
            $html .= sprintf("<table %s>", html_params(array_merge(['id' => $field->id], $options)));
        }

        foreach ($field as $subfield) {
            if (in_array($subfield->type, ['HiddenField', 'CSRFTokenField'])) {
                $hidden .= (string)$subfield;
            } else {
                $html .= sprintf("<tr><th>%s</th><td>%s%s</td></tr>", $subfield->label, $hidden, $subfield);
                $hidden = "";
            }
        }

        if ($this->with_table_tag) {
            $html .= "</table>";
        }

        if ($hidden) {
            $html .= $hidden;
        }

        return $html;
    }
}
