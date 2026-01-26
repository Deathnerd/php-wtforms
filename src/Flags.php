<?php

declare(strict_types=1);

namespace WTForms;

/**
 * Holds a set of boolean flags for a field.
 *
 * Flags are set by validators and widgets to communicate metadata
 * to templates and rendering logic (e.g., required indicators).
 */
class Flags
{
  /**
   * Field is required (set by InputRequired, DataRequired validators).
   */
  public bool $required = false;

  /**
   * Field is optional (set by Optional validator).
   */
  public bool $optional = false;

  /**
   * Field should be hidden (set by HiddenInput widget).
   */
  public bool $hidden = false;

  /**
   * Set a flag by name.
   *
   * @throws \InvalidArgumentException if flag name is unknown
   */
  public function set(string $flag, bool $value = true): void
  {
    if (!property_exists($this, $flag)) {
      throw new \InvalidArgumentException(sprintf('Unknown flag "%s". Known flags: required, optional, hidden', $flag));
    }
    $this->$flag = $value;
  }

  /**
   * Check if a flag is set.
   */
  public function has(string $flag): bool
  {
    if (!property_exists($this, $flag)) {
      return false;
    }

    return $this->$flag;
  }
}
