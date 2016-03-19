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
   * @param string $fieldname The name of the other field to compare to
   * @param string $message   Error message to raise in case of a validation error.
   */
  public function __construct($fieldname, $message = "")
  {
    $this->fieldname = $fieldname;
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
      $d = [
          "other_label" => property_exists($other, 'label') ? $other->label->text : $this->fieldname,
          "other_name"  => $this->fieldname,
      ];
      $message = $this->message;
      if ($message == "") {
        $message = "Field must be equal to $d[other_name].";
      }
      throw new ValidationError($message);
    }
  }
}