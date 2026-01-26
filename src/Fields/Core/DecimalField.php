<?php

// TODO Convert to LocaleAwareNumberField
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 4:06 PM.
 */

namespace WTForms\Fields\Core;

/**
 * A text field which stores and displays as a string, representing a decimal number.
 *
 * Currently this is an alias for FloatField, but will eventually handle
 * locale-aware decimal formatting and arbitrary precision via string representation.
 *
 * @property float|null $data
 */
class DecimalField extends FloatField
{
}
