<?php

declare(strict_types=1);

namespace WTForms\Validators\Options;

/**
 * Options for the RequiredIf validator.
 *
 * @phpstan-type RequiredIfOptionsArray array{fieldname?:string}
 * @extends ArrayOptions<RequiredIfOptionsArray>
 */
class RequiredIfOptions extends ArrayOptions
{
    public string $fieldname = '';

    protected function getDefaults(): array
    {
        return ['fieldname' => ''];
    }
}
