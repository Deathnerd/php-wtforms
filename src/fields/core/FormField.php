<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 5/17/2016
 * Time: 2:46 PM
 */

namespace WTForms\Fields\Core;

use WTForms\Forms;

/**
 * Encapsulate a form as a field into another form
 * @package WTForms\Fields\Core
 */
class FormField extends Field
{

  public $separator;
  protected $form_class;
  private $_obj;

  /**
   *
   *
   * @param string $label
   * @param array  $options
   *
   * @throws \TypeError
   */
  public function __construct($label, array $options)
  {
    if (!$options['form_class']) {
      throw new \TypeError("FormField must have a form_class argument passed to it via the options array!");
    }

    $this->form_class = new \ReflectionClass($options['form_class']);
    $this->separator = $options['separator'] ?: "-";
    $this->_obj = null;

    parent::__construct($label, $options);

    if ($this->filters) {
      throw new \TypeError("FormField cannot take filters, as the encapsulated data is not mutable");
    }

    if ($options['validators']) {
      throw new \TypeError("FormField does not accept any validators. Instead, define them on the enclosed form.");
    }
  }

  /**
   * @inheritdoc
   */
  public function process($formdata, $data = null)
  {
    if (is_null($data) || !$data) {
      if (is_callable($this->default)) {
        $data = $this->default->__invoke();
      } else {
        $data = $this->default;
      }
      $this->_obj = $data;
    }

    $this->object_data = $data;
    $prefix = $this->name . $this->separator;

    if (is_array($data)) {
      $this->form = Forms::createWithOptions($this->form_class, ["prefix" => $prefix], $formdata, $data);
    } else {
      $this->form = Forms::createWithOptions($this->form_class, ["prefix" => $prefix], $formdata, [], [], $data);
    }
  }
}