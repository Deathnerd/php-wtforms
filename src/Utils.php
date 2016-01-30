<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 5:14 PM
 */

namespace Deathnerd\WTForms\Utils;


/**
 * An unset value.
 *
 * This is used in situations where a blank value like `null` is acceptable
 * usually as the default value of a class variable or function parameter
 * (iow, usually when `null` is a valid value.)
 * @package Deathnerd\WTForms
 */
class UnsetValue
{
    public function __toString()
    {
        return "<unset value>";
    }

    public function bool()
    {
        return false;
    }

    public function nonzero()
    {
        return false;
    }
}

class Meta
{
    public $bases = [];

    /**
     * Meta constructor.
     * @param array $bases
     */
    public function __construct(array $bases = [])
    {
        $this->bases = $bases;
    }

}
