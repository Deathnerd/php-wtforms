<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/21/2016
 * Time: 11:16 AM
 */

namespace WTForms\Exceptions;


/**
 * Causes the validation chain to stop.
 *
 * If StopValidation is raised, no more validators in the validation chain are
 * called. If raised with a message, the message will be added to the errors
 * list.
 */
class StopValidation extends \Exception
{
}