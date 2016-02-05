<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/4/2016
 * Time: 11:15 AM
 */

namespace Deathnerd\WTForms\Tests\common;

use Deathnerd\WTForms\Fields\Core\Field;
use Deathnerd\WTForms\i18n\DummyTranslations;

class DummyField extends Field
{
    /**
     * @var DummyTranslations
     */
    public $_translations;
    public $errors;
    public $data;
    public $raw_data;

    public function __construct($data, $errors = [], $raw_data = null)
    {
        $this->data = $data;
        $this->errors = $errors;
        $this->raw_data = $raw_data;
        $this->_translations = new DummyTranslations();
    }

    public function gettext($string)
    {
        return $this->_translations->gettext($string);
    }

    public function ngettext($singular, $plural, $n)
    {
        return $this->_translations->ngettext($singular, $plural, $n);
    }

}