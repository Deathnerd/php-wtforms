<?php

declare(strict_types=1);

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;
use WTForms\Internal\Types\ArrayOptions;

/**
 * Base class for widgets that render fields as HTML.
 *
 * @template TData The type of data the associated field holds
 */
abstract class Widget
{
  /**
   * @var list<string>
   */
  public array $field_flags = [];

  /**
   * Construct from a typed Options object.
   */
  protected function constructFromOptions(ArrayOptions $options): void
  {
    $options->validate();
    $options->hydrateObject($this);
    $this->postHydrate();
  }

  /**
   * Hook for subclasses to run post-construction derived state.
   */
  protected function postHydrate(): void
  {
  }

  /**
   * Render the field as HTML.
   *
   * @param Field<TData>         $field   The field to render
   * @param array<string, mixed> $options HTML attributes to add to the rendered element
   */
  abstract public function __invoke(Field $field, array $options = []): string;
}
