<?php

declare(strict_types=1);

namespace WTForms\Fields\Options;

use WTForms\Fields\Core\SelectField;

/**
 * Options class for SelectField constructors.
 *
 * @phpstan-type ChoiceItem array{0: string|int, 1: string}
 * @phpstan-import-type FieldOptionsArray from FieldOptions
 * @phpstan-type SelectFieldOptionsArray array{
 *     choices?: array<int, ChoiceItem>,
 *     coerce?: callable|null
 * } & FieldOptionsArray
 * @extends FieldOptions<SelectFieldOptionsArray>
 */
class SelectFieldOptions extends FieldOptions
{
  /**
   * List of choices in the format [[value, label], ...].
   *
   * @var array<int, ChoiceItem>
   */
  public array $choices = [];

  /**
   * Callable to coerce values.
   *
   * @var callable|null
   */
  public $coerce;

  /**
   * @phpstan-param SelectField $obj
   * @return SelectField
   */
  public function hydrateObject(object $obj): object
  {
    parent::hydrateObject($obj);
    $obj->choices = $this->choices;
    if ($this->coerce !== null) {
      $obj->coerce = $this->coerce;
    }
    return $obj;
  }

  protected function getDefaults(): array
  {
    return array_merge(parent::getDefaults(), [
      'choices' => [],
      'coerce'  => null,
    ]);
  }
}
