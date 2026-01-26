<?php

declare(strict_types=1);

namespace WTForms\Fields\Options;

/**
 * Options class for DateTimeField constructors.
 *
 * @phpstan-type DateTimeFieldOptionsArray array{
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
 *     format?: string
 * }
 */
class DateTimeFieldOptions extends FieldOptions
{
  /**
   * The strftime-style format string for parsing/displaying dates.
   */
  public string $format = '%Y-%m-%d %H:%i:%s';

  protected function getDefaults(): array
  {
    return array_merge(parent::getDefaults(), [
      'format' => '%Y-%m-%d %H:%i:%s',
    ]);
  }
}
