<?php

declare(strict_types=1);

namespace WTForms\Validators\Options;

/**
 * Options for the UUID validator.
 *
 * @phpstan-type UUIDOptionsArray array{regex?:non-empty-string}
 *
 * @extends ArrayOptions<UUIDOptionsArray>
 */
class UUIDOptions extends ArrayOptions
{
  /** @var non-empty-string */
  public string $regex = '/^[0-9a-fA-F]{8}-([0-9a-fA-F]{4}-){3}[0-9a-fA-F]{12}$/';

  protected function getDefaults(): array
  {
    return ['regex' => '/^[0-9a-fA-F]{8}-([0-9a-fA-F]{4}-){3}[0-9a-fA-F]{12}$/'];
  }
}
