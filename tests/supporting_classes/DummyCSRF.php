<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 6/6/2016
 * Time: 10:53 PM
 */

namespace WTForms\Tests\SupportingClasses;


use WTForms\CSRF\Core\CSRF;
use WTForms\CSRF\Core\CSRFTokenField;

class DummyCSRF extends CSRF
{
  public function generateCSRFToken(/** @noinspection PhpUnusedParameterInspection */
      CSRFTokenField $csrf_token_field)
  {
    return 'dummytoken';
  }


}