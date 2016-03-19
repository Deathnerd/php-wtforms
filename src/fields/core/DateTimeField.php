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
  public $format;

  /**
   * @inheritdoc
   */
  public function __construct($label, array $kwargs)
  {
    parent::__construct($label, $kwargs);
    $kwargs = array_merge(["format" => "Y-m-d H:M:S"], $kwargs);
    $this->format = $kwargs['format'];
  }

  /**
   * @return string
   */
  public function _value()
  {
    if ($this->raw_data !== null) {
      return implode(" ", $this->raw_data);
    }

    return $this->data instanceof DateTime ? $this->data->format($this->format) : '';
  }

  /**
   * @param array $valuelist
   *
   * @throws ValueError
   */
  public function process_formdata(array $valuelist)
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
