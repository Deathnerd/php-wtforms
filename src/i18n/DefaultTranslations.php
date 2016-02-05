<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/4/2016
 * Time: 5:51 PM
 */

namespace Deathnerd\WTForms\i18n;

use Gettext\GettextTranslator;

/**
 * An object to wrap translations objects which use
 * @package Deathnerd\WTForms
 */
class DefaultTranslations
{

    /**
     * @var GettextTranslator The translation
     */
    public $translations;

    /**
     * DefaultTranslations constructor.
     * @param GettextTranslator $translations
     */
    public function __construct(GettextTranslator $translations)
    {
        $this->translations = $translations;
    }

    public function gettext($string)
    {
        return $this->translations->gettext($string);
    }

    public function ngettext($singular, $plural, $n)
    {
        return $this->translations->ngettext($singular, $plural, $n);
    }
}