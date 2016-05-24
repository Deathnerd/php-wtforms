<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:36 PM
 */

namespace WTForms\Fields\Core;

use DateTime;
use Exception;
use WTForms\Form;
use WTForms\ValueError;

/**
 * A text field which stores a `DateTime` matching a format
 * @package WTForms\Fields\Core
 */
class DateTimeField extends Field
{
  /**
   * @var string
   */
  public $format = "Y-m-d H:M:S";

  /**
   * @inheritdoc
   */
  public function __construct(array $options = [], Form $form = null)
  {
    if (array_key_exists('format', $options)) {
      $this->format = $options['format'];
      unset($options['format']);
    }
    parent::__construct($options);
  }


  public function __get($name)
  {
    if(in_array($name, ["value"])){
      if ($this->raw_data) {
        return implode(" ", $this->raw_data);
      }

      return $this->data instanceof DateTime ? $this->data->format($this->format) : '';
    }
    return null;
  }

  /**
   * @param array $valuelist
   *
   * @throws ValueError
   */
  public function processFormData(array $valuelist)
  {
    if ($valuelist) {
      $date_str = implode(" ", $valuelist);
      try {
        $this->data = new DateTime($date_str);
      } catch (Exception $e) {
        $this->data = null;
        throw new ValueError("Not a valid datetime value.");
      }
    }
  }
}
