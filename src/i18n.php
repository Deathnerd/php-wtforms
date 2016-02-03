<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/4/2016
 * Time: 9:59 AM
 */

namespace Deathnerd\WTForms;

use Gettext\GettextTranslator;


/**
 * A translations object which simply returns unmodified strings.
 *
 * This is typically used when translations are disabled or if no
 * valid translations provider can be found.
 * @package Deathnerd\WTForms
 */
class DummyTranslations
{
    /**
     * @param $string
     * @return mixed
     */
    public function gettext($string)
    {
        return $string;
    }

    /**
     * @param $singular
     * @param $plural
     * @param int $n
     * @return mixed
     */
    public function ngettext($singular, $plural, $n = 1)
    {
        if ($n == 1) {
            return $singular;
        }
        return $plural;
    }
}

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