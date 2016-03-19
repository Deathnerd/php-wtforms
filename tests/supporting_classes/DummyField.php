<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/4/2016
 * Time: 11:15 AM
 */
namespace WTForms\Tests\SupportingClasses;

use WTForms\Fields\Core\Field;

class DummyField extends Field
{
  /**
   * @var array
   */
  public $errors;
  /**
   * @var mixed
   */
  public $data;
  /**
   * @var mixed
   */
  public $raw_data;

  public function __construct($label = '', array $kwargs = [])
  {
    $this->data = array_key_exists('data', $kwargs) ? $kwargs['data'] : null;
    $this->errors = array_key_exists('errors', $kwargs) ? $kwargs['errors'] : [];
    $this->raw_data = array_key_exists('raw_data', $kwargs) ? $kwargs['raw_data'] : null;
  }
}