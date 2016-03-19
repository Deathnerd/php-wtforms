<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/14/2016
 * Time: 7:45 PM
 */

namespace WTForms;

use mindplay\annotations\Annotation;

/**
 * @usage('inherited'=>true,'class'=>true,'property'=>true)
 */
class FormAnnotation extends Annotation
{
  /**
   * @var string The Class name to use as the meta class supporting this form
   */
  public $meta = 'WTForms\DefaultMeta';

  /**
   * @var bool Set to true to enable CSRF protection for the form, false to disable
   */
  public $csrf = true;

  /**
   * @var string Any string set here will be prefixed to names for each field in the form.
   *             Useful for differentiating form objects that map to very similar data objects.
   *             Can also be set at render time on the {@link WTForms\Form Form} with a method
   */
  public $prefix = "";
}