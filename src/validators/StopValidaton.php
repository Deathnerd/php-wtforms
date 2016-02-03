<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/21/2016
 * Time: 11:16 AM
 */

namespace Deathnerd\WTForms\Validators;


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
     * @var array
     */
    public $args = [];

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
        $this->args = func_get_args();
        parent::__construct($message, $code, $previous);
    }
}