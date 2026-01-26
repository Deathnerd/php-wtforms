<?php

declare(strict_types=1);

namespace WTForms\Fields\Core;

use WTForms\Exceptions\TypeError;
use WTForms\Exceptions\ValueError;
use WTForms\Fields\Options\SelectFieldOptions;
use WTForms\Form;
use WTForms\Widgets\Core\Select;

/**
 * No different from a normal select field, except this one can take (and
 * validate) multiple choices. You'll need to specify the HTML `size`
 * attribute to the select field when rendering.
 *
 * @phpstan-import-type SelectFieldOptionsArray from SelectFieldOptions
 *
 * @property array<int, string|int>|null $data
 */
class SelectMultipleField extends SelectField
{
  /**
   * The selected values.
   *
   * @var array<int, string|int>|null
   */
  public $data;

  /**
   * @param SelectFieldOptionsArray $options
   */
  public function __construct(array $options = ['choices' => []])
  {
    $options = array_merge(['widget' => new Select()], $options);
    parent::__construct($options);
    /** @var Select $widget */
    $widget = $this->widget;
    $widget->multiple = true;
  }

  /**
   * Provides data for choice widget rendering. Must return a sequence or
   * iterable of `[value,label,selected]` tuples.
   *
   * @return \Generator<int, array{0: string|int, 1: string, 2: bool}>
   */
  public function getChoices(): \Generator
  {
    foreach ($this->choices as list($value, $label)) {
      yield [$value, $label, null !== $this->data && in_array($value, $this->data)];
    }
  }

  /**
   * @param array<int, string|int>|null $value
   */
  public function processData($value): void
  {
    try {
      if (!is_array($value)) {
        throw new TypeError();
      }
      $d = [];
      foreach ($value as $v) {
        $d[] = ($this->coerce)($v);
      }
    } catch (ValueError $e) {
      $d = null;
    } catch (TypeError $e) {
      $d = null;
    }
    $this->data = $d;
  }

  /**
   * @param array<int, string> $valuelist
   *
   * @throws ValueError
   */
  public function processFormData(array $valuelist): void
  {
    try {
      $d = [];
      foreach ($valuelist as $v) {
        $d[] = ($this->coerce)($v);
      }
    } catch (ValueError $e) {
      throw new ValueError('Invalid choice(s): one or more data inputs could not be coerced');
    }
    $this->data = $d;
  }

  /**
   * @throws ValueError
   */
  public function preValidate(Form $form): void
  {
    if ($this->data) {
      $values = [];
      foreach ($this->choices as $c) {
        $values[] = $c[0];
      }
      foreach ($this->data as $d) {
        if (!in_array($d, $values)) {
          throw new ValueError("'$d' is not a valid choice for this field");
        }
      }
    }
  }
}
