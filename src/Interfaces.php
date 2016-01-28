<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/31/2015
 * Time: 10:48 AM
 */

namespace Deathnerd\WTForms\Interfaces;
use Deathnerd\WTForms\Fields\Field;
use Deathnerd\WTForms\Form;

/**
 * Interface for all WTForms HTML validators
 * @package Deathnerd\WTForms\Interfaces
 */
interface ValidatorInterface
{
    /**
     *
     * @param Form $form The current form
     * @param Field $field The field being validated
     * @param string $message The message display to the user if validation fails
     *
     * @return bool Did the validation pass?
     */
    public function validate(Form $form, Field $field, $message = null);
}