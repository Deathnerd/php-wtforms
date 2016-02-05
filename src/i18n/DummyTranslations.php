<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/4/2016
 * Time: 5:50 PM
 */

namespace Deathnerd\WTForms\i18n;


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