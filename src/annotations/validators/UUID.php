<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 11:21 PM
 */

namespace WTForms\Annotations\Validators;

/**
 * Validates a UUID
 * @package WTForms\Annotations\Validators
 * @Annotation
 */
class UUID extends Regexp
{

  /**
   * @var string
   */
  public $regex = '^[0-9a-fA-F]{8}-([0-9a-fA-F]{4}-){3}[0-9a-fA-F]{12}$';
}