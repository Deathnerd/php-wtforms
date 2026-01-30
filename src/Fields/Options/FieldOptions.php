<?php /** @noinspection PhpUnused */

declare(strict_types=1);

namespace WTForms\Fields\Options;

use WTForms\DefaultMeta;
use WTForms\Form;
use WTForms\Fields\Core\Field;
use WTForms\Internal\Types\ArrayOptions;
use WTForms\Validators\Validator;
use WTForms\Widgets\Core\Widget;

/**
 * Base options class for Field constructors.
 *
 * @template TDefault
 *
 * @phpstan-type FieldOptionsArray array{
 *     validators?: list<Validator|callable>,
 *     filters?: list<callable>,
 *     description?: string,
 *     id?: string|null,
 *     default?: TDefault,
 *     widget?: Widget|null,
 *     render_kw?: array<string, mixed>,
 *     name?: string|null,
 *     prefix?: string,
 *     meta?: DefaultMeta|null,
 *     attributes?: array<string, mixed>,
 *     label?: string|null,
 *     class?: string|null,
 *     form?: Form|null,
 *     type?: string,
 *     data?: mixed|array,
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

  /** @var TDefault $default */
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

  /**
   * Assign common field properties from options to the target object.
   *
   * @template TField of Field
   * @phpstan-param TField $obj
   * @return TField
   */
  public function hydrateObject(object $obj): object
  {
    return $obj;
  }

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
