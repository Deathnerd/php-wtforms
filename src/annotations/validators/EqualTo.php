<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/30/2015
 * Time: 3:46 PM
 */
namespace WTForms\Annotations\Validators;

/**
 * Compares the values of two fields
 * @package WTForms\Annotations\Validators
 * @Annotation
 */
class EqualTo extends ValidatorBase
{
  /**
   * @var string
   */
  public $fieldname;
}