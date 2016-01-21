<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/20/2016
 * Time: 10:10 PM
 */

namespace Deathnerd\WTForms\Widgets;


/**
 * Render a hidden input
 * @package Deathnerd\WTForms\Widgets
 */
class HiddenInput extends Input
{
    /**
     * @var array
     */
    public $field_flags = ['hidden'];
    public function __construct()
    {
        parent::__construct("hidden");
    }
}