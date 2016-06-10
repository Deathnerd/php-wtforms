<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 2/2/2016
 * Time: 2:36 PM
 */

namespace WTForms\Fields\Core;

use Carbon\Carbon;
use DateTime;
use Exception;
use WTForms\Exceptions\ValueError;
use WTForms\Widgets\Core\TextInput;

/**
 * A text field which stores a `DateTime` matching a format
 * @package WTForms\Fields\Core
 */
class DateTimeField extends Field
{
    /**
     * @var string
     */
    public $format = "%Y-%m-%d %H:%i:%s";
    protected $carbon_format = "Y-m-d H:i:s";

    /**
     * @inheritdoc
     */
    public function __construct(array $options = [])
    {
        if (array_key_exists('format', $options)) {
            $this->format = $options['format'];
            unset($options['format']);
        }
        $this->carbon_format = preg_replace('/%/', '', $this->format);
        $options = array_merge(["widget" => new TextInput()], $options);
        parent::__construct($options);
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

            if ($this->data instanceof Carbon) {
                return $this->data->formatLocalized(str_replace("%s", "%S", str_replace("%i", "%M", $this->format)));
            } elseif ($this->data instanceof \DateTime) {
                return Carbon::instance($this->data)
                             ->formatLocalized(str_replace("%s", "%S", str_replace("%i", "%M", $this->format)));
            } else {
                return ''; // @codeCoverageIgnore
            }
        }

        return parent::__get($name); // @codeCoverageIgnore
    }

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
                $this->data = Carbon::createFromFormat($this->carbon_format, $date_str);
            } catch (Exception $e) {
                $this->data = null;
                throw new ValueError("Not a valid datetime value");
            }
        }
    }
}
