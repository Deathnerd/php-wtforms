<?php

declare(strict_types=1);

namespace WTForms\Fields\Options;

/**
 * Options class for SelectField constructors.
 *
 * @phpstan-type SelectFieldOptionsArray array{
 *     validators?: list<\WTForms\Validators\Validator|callable>,
 *     filters?: list<callable>,
 *     description?: string,
 *     id?: string|null,
 *     default?: mixed,
 *     widget?: \WTForms\Widgets\Core\Widget|null,
 *     render_kw?: array<string, mixed>,
 *     name?: string|null,
 *     prefix?: string,
 *     meta?: \WTForms\DefaultMeta|null,
 *     attributes?: array<string, mixed>,
 *     label?: string|null,
 *     class?: string|null,
 *     form?: \WTForms\Form|null,
 *     choices?: array<int, array{0: string|int, 1: string}>,
 *     coerce?: callable|null
 * }
 */
class SelectFieldOptions extends FieldOptions
{
  /**
   * List of choices in the format [[value, label], ...].
   *
   * @var array<int, array{0: string|int, 1: string}>
   */
  public array $choices = [];

  /**
   * Callable to coerce values.
   *
   * @var callable|null
   */
  public $coerce;

  protected function getDefaults(): array
  {
    return array_merge(parent::getDefaults(), [
      'choices' => [],
      'coerce'  => null,
    ]);
  }
}
