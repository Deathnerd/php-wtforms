<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/4/2016
 * Time: 5:54 PM
 */

namespace Deathnerd\WTForms\Utils;


/**
 * @property  string csrf_context
 * @property  int csrf_time_limit
 * @property  string csrf_secret
 */
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