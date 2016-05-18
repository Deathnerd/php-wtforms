<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/17/2016
 * Time: 3:18 PM
 */

namespace WTForms\Annotations\Fields\Core;


use WTForms\Annotations\Field;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class FieldList extends Field
{
  /**
   * @var \WTForms\Annotations\Field
   */
  public $inner_field;

  /**
   * @var int
   */
  public $max_entries;

  /**
   * @var int
   */
  public $min_entries = 0;

  /**
   * @var string
   */
  public $widget = 'WTForms\Widgets\Core\ListWidget';

}