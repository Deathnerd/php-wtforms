<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/21/2016
 * Time: 11:12 AM
 */

namespace WTForms\Validators;


/**
 * Raised when a validator fails to validate its input
 */
class ValidationError extends \Exception
{
  /**
   * Raised when a validator fails to validate its input
   *
   * @param string     $message The message to display upon validation failure
   * @param int        $code
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