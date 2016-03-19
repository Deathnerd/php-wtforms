<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/15/2016
 * Time: 11:37 PM
 */

namespace WTForms\Tests\SupportingClasses;

/**
 * @form('prefix'=>'foo', 'csrf'=>false, 'meta'=>'\WTForms\Tests\SupportingClasses\FooMeta')
 */
class AnnotatedHelper
{
  /**
   * @var string
   * @stringField('id'=>'fname', 'classes'=>['form-control'])
   * @inputRequired
   */
  public $first_name;
}