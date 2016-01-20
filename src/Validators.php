<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/30/2015
 * Time: 3:46 PM
 */
namespace Deathnerd\WTForms;

use Deathnerd\WTForms\Interfaces\ValidatorInterface;

/**
 * Raised when a validator fails to validate its input
 */
class ValidationError extends \Exception
{
    /**
     * Raised when a validator fails to validate its input
     *
     * @param string $message The message to display upon validation failure
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return $this->message;
    }
}

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

/**
 * Causes the validation chain to stop.
 *
 * If StopValidation is raised, no more validators in the validation chain are
 * called. If raised with a message, the message will be added to the errors
 * list.
 */
class StopValidation extends \Exception
{
    /**
     * Causes the validation chain to stop.
     *
     * If StopValidation is raised, no more validators in the validation chain are
     * called. If raised with a message, the message will be added to the errors
     * list.
     *
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}


/**
 * Compares the values of two fields
 */
class EqualTo extends Validator
{

    public function validate($form, $field, $message = null)
    {

    }
}