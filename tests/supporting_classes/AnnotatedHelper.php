<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/15/2016
 * Time: 11:37 PM
 */

namespace WTForms\Tests\SupportingClasses;

use WTForms\Annotations\Fields\Core\BooleanField;
use WTForms\Annotations\Fields\Core\StringField;
use WTForms\Annotations\Form;
use WTForms\Annotations\Validators\AnyOf;
use WTForms\Annotations\Validators\InputRequired;


/**
 * @Form(prefix="foo", csrf=false, meta="\WTForms\Tests\SupportingClasses\FooMeta")
 */
class AnnotatedHelper
{
  /**
   * @var string
   * @StringField(id="fname", classes={"form-control"}, attributes={"data-required"},
   *   validators={ @InputRequired(message="This input is required, yo"),
   *                @AnyOf(message="You've gotta match these, guy $values", values={1,"foo",DIRECTORY_SEPARATOR})
   *   })
   */
  public $first_name;

  /**
   * @var string
   * @StringField
   */
  public $last_name;

  /**
   * @var boolean
   * @BooleanField()
   */
  public $bool1;
  /**
   * @var boolean
   * @BooleanField(default=true, false_values={})
   */
  public $bool2;

  /**
   * @var string
   * @StringField(default="hello", render_kw={"readonly"=true,"foo"="bar"})
   */
  public $a;
}