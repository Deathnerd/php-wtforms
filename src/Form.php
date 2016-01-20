<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/31/2015
 * Time: 11:12 AM
 */

namespace Deathnerd\WTForms;
use Illuminate\Support\Collection;


/**
 * Base Form Class.
 * Provides core behaviour like field construction, validation, and data and error proxying.
 * @package Deathnerd\WTForms
 */
class BaseForm
{

    /**
     * BaseForm constructor.
     */
    public function __construct(Collection $fields, $prefix="")
    {
    }
}

class Form extends BaseForm{

}