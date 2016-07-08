<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 3:14 PM
 */

namespace WTForms\Fields\Core;

use Carbon\Carbon;
use WTForms\Exceptions\ValueError;

/**
 * Same as DateTimeField, except stores a date (actually still a DateTime,
 * but formats to just a Date).
 * @package WTForms\Fields\Core
 */
class DateField extends DateTimeField
{
    public $format = "%Y-%m-%d";

    /**
     * @param array $valuelist
     *
     * @throws ValueError
     */
    public function processFormData(array $valuelist)
    {
        if ($valuelist) {
            $date_str = implode(" ", $valuelist);
            try {
                $this->data = Carbon::createFromFormat($this->carbon_format, $date_str)->startOfDay();
            } catch (\Exception $e) {
                $this->data = null;
                // Do not create a process error if this is an optional field
                if (!empty($date_str) && !$this->flags->optional) {
                    throw new ValueError("Not a valid date value");
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if ($name == "value") {
            if ($this->raw_data) {
                return implode(" ", $this->raw_data);
            }

            if ($this->data instanceof \Carbon\Carbon) {
                return $this->data->startOfDay()->formatLocalized($this->format);
            } elseif ($this->data instanceof \DateTime) {
                return \Carbon\Carbon::instance($this->data)->startOfDay()->formatLocalized($this->format);
            } else {
                return '';
            }
        }

        return parent::__get($name); // @codeCoverageIgnore
    }
}

