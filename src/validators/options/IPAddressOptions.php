<?php

declare(strict_types=1);

namespace WTForms\Validators\Options;

/**
 * Options for the IPAddress validator.
 *
 * @phpstan-type IPAddressOptionsArray array{ip_type?:int}
 *
 * @extends ArrayOptions<IPAddressOptionsArray>
 */
class IPAddressOptions extends ArrayOptions
{
  public int $ip_type = FILTER_FLAG_IPV4;

  protected function getDefaults(): array
  {
    return ['ip_type' => FILTER_FLAG_IPV4];
  }
}
