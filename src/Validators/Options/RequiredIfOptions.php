<?php

declare(strict_types=1);

namespace WTForms\Validators\Options;

use WTForms\Internal\Types\ArrayOptions;

/**
 * Options for the RequiredIf validator.
 *
 * @phpstan-type RequiredIfOptionsArray array{fieldname?:string}
 *
 * @extends ArrayOptions<RequiredIfOptionsArray>
 */
class RequiredIfOptions extends ArrayOptions
{
  public string $fieldname = '';

  public function validate(): void
  {
    if ($this->fieldname === '') {
      throw new \RuntimeException('RequiredIf requires fieldname!');
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
