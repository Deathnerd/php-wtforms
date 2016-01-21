<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/21/2016
 * Time: 11:15 AM
 */

namespace Deathnerd\WTForms\Validators;


use Deathnerd\WTForms\Interfaces\ValidatorInterface;

class Validator implements ValidatorInterface
{
    /**
     * @inheritdoc
     */
    public function validate($form, $field, $message = null)
    {
        throw new \RuntimeException;
    }
}