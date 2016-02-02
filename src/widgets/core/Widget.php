<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/18/2016
 * Time: 3:46 PM
 */

namespace Deathnerd\WTForms\Widgets\Core;
use Deathnerd\WTForms\NotImplemented;

/**
 * Just so I can have type hinting for widgets
 * @package Deathnerd\WTForms\Widgets
 */
class Widget
{
    public function __invoke(...$args){
        throw new NotImplemented("Widget needs an __invoke method");
    }
}
