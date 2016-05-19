<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 4/8/2016
 * Time: 6:24 PM
 */

namespace WTForms\Tests;

use WTForms\Annotations\Fields\Core\BooleanField;
use WTForms\Annotations\Fields\Core\SelectField;
use WTForms\Annotations\Fields\Core\StringField;
use WTForms\Annotations\Form;
use WTForms\Annotations\Validators\EqualTo;
use WTForms\Annotations\Validators\InputRequired;
use WTForms\Annotations\Validators\NoneOf;

/**
 * Class IndexTestForm
 * @package WTForms\Tests
 * @Form(csrf=false, prefix="index_")
 */
class IndexTestForm
{
  /**
   * @var string
   * @StringField(label="What's your name?", default="Stan Foo", render_kw={"placeholder"="Stan Foo"},
   *   validators={
   *    @InputRequired(message="You gotta tell me your name, brah")
   *   })
   */
  public $name;

  /**
   * @var boolean
   * @SelectField(label="Do you think WTForms is amazing?",
   *   default="yes", choices={{"yes", "I sure do!"}, {"no", "Nope!"}},
   *   validators={
   *   @NoneOf(values={"no"}, message="You and I won't get along then!"),
   *   @InputRequired(message="You've gotta tell me if you think WTForms is awesome!")
   * })
   */
  public $wtforms_is_awesome;

  /**
   * @var string
   * @StringField(label="Make this thing...", validators={@EqualTo(fieldname="thing_dos")})
   */
  public $thing_uno;

  /**
   * @var string
   * @StringField(label="... equal this thing")
   */
  public $thing_dos;

  /**
   * @var boolean
   * @BooleanField
   */
  public $yet_another_thing;

  /**
   * @var boolean
   * @BooleanField(label="And one more thing!")
   */
  public $and_one_more_thing;
}