<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/4/2016
 * Time: 6:32 PM
 */

namespace Deathnerd\WTForms\Interfaces;


interface FormatterInterface
{
    public function run(...$values);
}