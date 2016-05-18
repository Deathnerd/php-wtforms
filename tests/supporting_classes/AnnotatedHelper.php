<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/15/2016
 * Time: 11:37 PM
 */

namespace WTForms\Tests\SupportingClasses;

use WTForms\Annotations\Fields\Core\BooleanField;
use WTForms\Annotations\Fields\Core\SelectField;
use WTForms\Annotations\Fields\Core\SelectMultipleField;
use WTForms\Annotations\Fields\Core\StringField;
use WTForms\Annotations\Form;
use WTForms\Annotations\Validators\AnyOf;
use WTForms\Annotations\Validators\DataRequired;
use WTForms\Annotations\Validators\InputRequired;
use WTForms\Annotations\Fields\Core\FieldList;


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

  /**
   * @var array
   * @SelectField(choices={{"a", "hello"}, {"btest", "bye"}}, default="a")
   */
  public $select_a;

  /**
   * @var array
   * @SelectField(choices={{1,"Item 1"},{2,"Item 2"}}, option_widget="WTForms\Widgets\Core\TextInput", coerce="intval")
   */
  public $select_b;

  /**
   * @var array
   * @SelectField(choices={{0,"Foo"},{1,"Bar"},{2,"Baz"}}, default=0, multiple=true)
   */
  public $select_multiple;

  /**
   * @var FieldList
   * @FieldList(inner_field=@StringField(validators={@DataRequired}))
   */
  public $list_of_fields;
}