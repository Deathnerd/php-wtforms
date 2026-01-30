<?php

declare(strict_types=1);

namespace WTForms\Fields\Options;

use WTForms\Exceptions\TypeError;
use WTForms\Form;

/**
 * Options class for FormField constructors.
 *
 * @phpstan-import-type FieldOptionsArray from FieldOptions
 * @phpstan-type FormFieldOptionsArray array{
 *     form_class?: class-string<Form>|Form,
 *     separator?: string
 * } & FieldOptionsArray
 * @extends FieldOptions<FormFieldOptionsArray>
 */
class FormFieldOptions extends FieldOptions
{
  /** @var class-string<Form>|Form|null */
  public Form|string|null $form_class = null;

  public string $separator = '-';

  /**
   * @throws TypeError
   */
  public function validate(): void
  {
    if ($this->form_class === null) {
      throw new TypeError('FormField must have a form_class property set!');
    }
    if (!empty($this->filters)) {
      throw new TypeError('FormField cannot take filters, as the encapsulated data is not mutable');
    }
    if (!empty($this->validators)) {
      throw new TypeError('FormField does not accept any validators. Instead, define them on the enclosed form.');
    }
  }

  /**
   * @phpstan-param \WTForms\Fields\Core\FormField $obj
   * @return \WTForms\Fields\Core\FormField
   */
  public function hydrateObject(object $obj): object
  {
    $obj->form_class = $this->form_class;
    $obj->separator = $this->separator;
    return $obj;
  }

  protected function getDefaults(): array
  {
    return array_merge(parent::getDefaults(), [
      'form_class' => null,
      'separator' => '-',
    ]);
  }
}
