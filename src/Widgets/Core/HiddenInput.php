<?php

declare(strict_types=1);

namespace WTForms\Widgets\Core;

/**
 * Render a hidden input.
 *
 * @extends Input<mixed>
 */
class HiddenInput extends Input
{
  /** @var list<string> */
  public array $field_flags = ['hidden'];

  public string $type = 'hidden';

  public function __construct()
  {
    parent::__construct('hidden');
  }
}
