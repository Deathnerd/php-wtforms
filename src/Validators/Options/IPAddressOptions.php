<?php

declare(strict_types=1);

namespace WTForms\Validators\Options;

use WTForms\Internal\Types\ArrayOptions;

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

  public function hydrateObject(object $obj): object
  {
    $obj->ip_type = $this->ip_type;
    return $obj;
  }

  protected function getDefaults(): array
  {
    return ['ip_type' => FILTER_FLAG_IPV4];
  }
}
