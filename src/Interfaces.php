<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/31/2015
 * Time: 10:48 AM
 */

namespace Deathnerd\WTForms\Interfaces;

/**
 * Interface for all WTForms HTML validators
 * @package Deathnerd\WTForms\Interfaces
 */
interface ValidatorInterface
{
    /**
     *
     * @todo Update the $form type when one is made
     * @todo Update the $field type when one is made
     *
     * @param object $form The current form
     * @param object $field The field being validated
     * @param null $message The message display to the user if validation fails
     *
     * @return bool Did the validation pass?
     */
    public function validate($form, $field, $message = null);
}