<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/3/2016
 * Time: 4:17 PM
 */

namespace Deathnerd\WTForms\widgets\core;

use Deathnerd\WTForms\Fields\Core\Field;
use Illuminate\Support\HtmlString;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Renders a list of fields as a set of table rows with th/td pairs.
 *
 * If `$with_table_tag` is true, then an enclosing `<table>` is placed around the
 * rows.
 *
 * Hidden fields will not be displayed with a row, instead the field will be
 * pushed into a subsequent table row to ensure XHTML validity. Hidden fields
 * at the end of the field list will appear outside the table.
 * @package Deathnerd\WTForms\widgets\core
 */
class TableWidget extends Widget
{
    /**
     * @var bool If true, an enclosing `<table>` is placed around the rows.
     */
    public $with_table_tag;

    /**
     * TableWidget constructor.
     * @param bool $with_table_tag
     */
    public function __construct($with_table_tag = true)
    {
        $this->with_table_tag = $with_table_tag;
    }

    /**
     * @param Field $field
     * @param array $kwargs
     * @return HtmlString
     */
    public function __invoke(Field $field, array $kwargs = [])
    {
        $html = "";
        $hidden = "";

        if ($this->with_table_tag) {
            $resolver = new OptionsResolver();
            $resolver->setDefault("id", $field->id);
            $html .= sprintf("<input %s>", html_params($resolver->resolve($kwargs)));
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

        return new HtmlString($html);
    }


}