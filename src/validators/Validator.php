<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/21/2016
 * Time: 11:15 AM
 */

namespace Deathnerd\WTForms\Validators;

use Deathnerd\WTForms\BaseForm;
use Deathnerd\WTForms\Fields\Core\Field;
use Deathnerd\WTForms\NotImplemented;

class Validator
{
    /**
     * @var string Error message to raise in case of a validation error
     */
    public $message;
    public $field_flags = [];

    /**
     * @param BaseForm $form
     * @param Field $field
     * @param string $message
     * @throws NotImplemented
     */
    function __invoke(BaseForm $form, Field $field, $message = "")
    {
        throw new NotImplemented("Validator must have an overridden __invoke method");
    }


}
