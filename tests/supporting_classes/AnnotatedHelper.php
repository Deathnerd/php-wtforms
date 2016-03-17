<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/15/2016
 * Time: 11:37 PM
 */

namespace wtforms\tests\supporting_classes;

/**
 * @form('prefix'=>'foo', 'csrf'=>false, 'meta'=>'\wtforms\tests\supporting_classes\FooMeta')
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