<?php

declare(strict_types=1);

namespace WTForms\Fields\Options;

use WTForms\Fields\Core\SelectField;
use WTForms\Internal\Types\IdentityClosure;
use WTForms\Internal\Types\OpenClosure;
use WTForms\Internal\Types\SelectFieldChoiceList;

/**
 * Options class for SelectField constructors.
 *
 * @phpstan-type ChoiceItem array{0: string|int, 1: string}
 *
 * @phpstan-import-type FieldOptionsArray from FieldOptions
 *
 * @phpstan-type SelectFieldOptionsArray array{
 *     choices?: array<int, ChoiceItem>,
 *     coerce?: callable|null
 * } & FieldOptionsArray
 *
 * @extends FieldOptions<SelectFieldOptionsArray>
 */
class SelectFieldOptions extends FieldOptions
{
  /**
   * @param SelectFieldChoiceList $choices List of choices in the format [[value, label], ...].
   * @param OpenClosure           $coerce  callable to coerce values
   */
  public function __construct(
    public SelectFieldChoiceList $choices = new SelectFieldChoiceList(),
    public OpenClosure $coerce = new IdentityClosure(),
    FieldOptions $data = new FieldOptions(),
  ) {
    parent::__construct($data);
  }

  /**
   * @phpstan-param SelectField $obj
   *
   * @return SelectField
   */
  public function hydrateObject(object $obj): object
  {
    parent::hydrateObject($obj);
    $obj->choices = $this->choices;
    $obj->coerce = $this->coerce;

    return $obj;
  }

  protected function getDefaults(): array
  {
    return array_merge(parent::getDefaults(), [
      'choices' => $this->choices,
      'coerce'  => $this->coerce,
    ]);
  }
}
