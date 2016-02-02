<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 3:14 PM
 */

namespace Deathnerd\WTForms\Fields\Core;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Same as DateTimeField, except stores a date (actually still a DateTime,
 * but formats to just a Date).
 * @package Deathnerd\WTForms\Fields\Core
 */
class DateField extends DateTimeField
{
    /**
     * @inheritdoc
     */
    public function __construct($label, array $kwargs)
    {
        $kwargs = (new OptionsResolver())->setDefault("format", "Y-m-d")->resolve($kwargs);
        parent::__construct($label, $kwargs);
    }
}
