<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/18/2016
 * Time: 1:23 PM
 */

namespace WTForms\Annotations\Fields\Core;


use WTForms\Annotations\Field;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class FormField extends Field
{
  /**
   * @var string
   */
  public $separator = "-";

  /**
   * @var string
   */
  public $form_class;

  /**
   * @var string
   */
  public $widget = 'WTForms\Widgets\Core\TableWidget';
}