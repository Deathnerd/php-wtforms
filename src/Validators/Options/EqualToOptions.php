<?php

declare(strict_types=1);

namespace WTForms\Validators\Options;

use WTForms\Internal\Types\ArrayOptions;

/**
 * Options for the EqualTo validator.
 *
 * @phpstan-type EqualToOptionsArray array{fieldname?:string}
 *
 * @extends ArrayOptions<EqualToOptionsArray>
 */
class EqualToOptions extends ArrayOptions
{
  public string $fieldname = '';

  public function validate(): void
  {
    if ($this->fieldname === '') {
      throw new \RuntimeException('EqualTo requires fieldname!');
    }
  }

  public function hydrateObject(object $obj): object
  {
    $obj->fieldname = $this->fieldname;
    return $obj;
  }

  protected function getDefaults(): array
  {
    return ['fieldname' => ''];
  }
}
