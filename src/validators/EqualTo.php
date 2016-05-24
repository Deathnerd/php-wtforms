<?php

/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/30/2015
 * Time: 3:46 PM
 */
namespace WTForms\Validators;

use WTForms\Fields\Core\Field;
use WTForms\Form;

/**
 * Compares the values of two fields
 */
class EqualTo extends Validator
{
  /**
   * @var string
   */
  public $fieldname;

  /**
   * TODO: Interpolation of other_label and other_name
   *
   * @param string $message Error message to raise in case of a validation error.
   * @param array  $options
   *
   * @internal param string $fieldname The name of the other field to compare to
   */
  public function __construct($message = "", array $options = ['fieldname' => ''])
  {
    if (!$options['fieldname']) {
      throw new \RuntimeException("EqualTo requires fieldname!");
    }
    $this->fieldname = $options['fieldname'];
    $this->message = $message;
  }

  /**
   * @param Form   $form
   * @param Field  $field
   * @param string $message
   *
   * @throws ValidationError
   */
  public function __invoke(Form $form, Field $field, $message = "")
  {
    if ($form->{$this->fieldname} !== null) {
      /** @var Field $other */
      $other = $form->{$this->fieldname};
    } else {
      throw new ValidationError("Invalid field name '{$this->fieldname}'");
    }

    if ($field->data != $other->data) {
      $message = $this->message;
      list($other_label, $other_name) = $this->formatter($other);
      if ($message == "") {
        $message = sprintf("Field must be equal to %s", $other_name);
      } else {
        $message = sprintf($message, $other_name, $other_label);
      }
      throw new ValidationError($message);
    }
  }

  protected function formatter(Field $other)
  {
    if (!is_null($this->user_formatter) && is_callable($this->user_formatter)) {
      return $this->user_formatter->__invoke($other);
    }

    return [
        "other_label" => property_exists($other, 'label') ? $other->label->text : $this->fieldname,
        "other_name"  => $this->fieldname,
    ];
  }
}