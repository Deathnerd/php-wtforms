<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/14/2016
 * Time: 9:33 PM
 */

namespace WTForms\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;


/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Field
{
  /**
   * @var mixed The value(s) held in this field, either from user submission or pre-population
   */
  public $value;
  /**
   * @var string The corresponding label for this field. If no explicit value is given, this is pre-populated
   *             with the name of the field property in the class declaration
   */
  public $label;
  /**
   * @var string Identifies the field as a specific type (text input, select, range input, etc). Do not manually
   *      override in the annotation declaration. It will be overridden by the respective annotation anyways
   */
  public $type;
  /**
   * @var string Identifies the class name to be used as a rendering widget. Normally this is supplied by the Field upon
   *             instantiation, but can be overridden with a class name on the annotation.
   */
  public $widget;
  /**
   * @var array Key=>value array signifying any extra HTML attributes to be included on the form field at render time
   */
  public $attributes = [];
  /**
   * @var string The `name` HTML attribute for this field element
   */
  public $name;
  /**
   * @var string The `id` HTML attribute for this field element
   */
  public $id;
  /**
   * @var array A list of classes to be applied to the field element at render time
   */
  public $classes = [];

  /**
   * @var array
   */
  public $validators = [];

  /**
   * @var mixed
   */
  public $default;

  /**
   * @var array
   */
  public $render_kw = [];
}