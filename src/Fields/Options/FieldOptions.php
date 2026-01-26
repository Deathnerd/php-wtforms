<?php

declare(strict_types=1);

namespace WTForms\Fields\Options;

use WTForms\DefaultMeta;
use WTForms\Form;
use WTForms\Internal\Types\ArrayOptions;
use WTForms\Validators\Validator;
use WTForms\Widgets\Core\Widget;

/**
 * Base options class for Field constructors.
 *
 * @phpstan-type FieldOptionsArray array{
 *     validators?: list<Validator|callable>,
 *     filters?: list<callable>,
 *     description?: string,
 *     id?: string|null,
 *     default?: mixed,
 *     widget?: Widget|null,
 *     render_kw?: array<string, mixed>,
 *     name?: string|null,
 *     prefix?: string,
 *     meta?: DefaultMeta|null,
 *     attributes?: array<string, mixed>,
 *     label?: string|null,
 *     class?: string|null,
 *     form?: Form|null
 * }
 *
 * @template TOptions of array<string, mixed>
 *
 * @extends ArrayOptions<TOptions>
 */
class FieldOptions extends ArrayOptions
{
  /**
   * @var list<Validator|callable>
   */
  public array $validators = [];

  /**
   * @var list<callable>
   */
  public array $filters = [];

  public string $description = '';

  public ?string $id = null;

  public $default;

  public ?Widget $widget = null;

  /**
   * @var array<string, mixed>
   */
  public array $render_kw = [];

  public ?string $name = null;

  public string $prefix = '';

  public ?DefaultMeta $meta = null;

  /**
   * @var array<string, mixed>
   */
  public array $attributes = [];

  public ?string $label = null;

  public ?string $class = null;

  public ?Form $form = null;

  protected function getDefaults(): array
  {
    return [
      'validators'  => [],
      'filters'     => [],
      'description' => '',
      'id'          => null,
      'default'     => null,
      'widget'      => null,
      'render_kw'   => [],
      'name'        => null,
      'prefix'      => '',
      'meta'        => null,
      'attributes'  => [],
      'label'       => null,
      'class'       => null,
      'form'        => null,
    ];
  }
}
