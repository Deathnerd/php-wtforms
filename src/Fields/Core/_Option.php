<?php

namespace WTForms\Fields\Core;

/**
 * Internal class representing a single option in a select field.
 *
 * @extends Field<string|int|bool|null>
 *
 * @internal
 */
class _Option extends Field
{
  /**
   * Whether this option is currently checked/selected.
   */
  public bool $checked = false;

  public function __get(string $name): string
  {
    if (is_bool($this->data)) {
      return $this->data ? 'true' : 'false';
    }

    return $this->data;
  }
}
