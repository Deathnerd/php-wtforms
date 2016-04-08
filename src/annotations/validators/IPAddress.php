<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 10:24 PM
 */

namespace WTForms\Annotations\Validators;


/**
 * Validates an IP Address
 * @package WTForms\Annotations\Validators
 * @Annotation
 */
class IPAddress extends ValidatorBase
{
  /**
   * @var int
   */
  public $ip_type;
}