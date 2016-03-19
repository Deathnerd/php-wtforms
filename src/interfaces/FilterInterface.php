<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/27/2016
 * Time: 4:35 PM
 */

namespace WTForms\Interfaces;


interface FilterInterface
{
    public static function run($data);
}