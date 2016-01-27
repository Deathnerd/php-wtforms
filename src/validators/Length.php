<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/22/2016
 * Time: 8:21 PM
 */

namespace Deathnerd\WTForms\Validators;


use Deathnerd\WTForms\Fields\Field;

/**
 * Validates the length of a string
 * @package Deathnerd\WTForms\Validators
 */
class Length extends Validator
{
    /**
     * @var int
     */
    public $min;
    /**
     * @var int
     */
    public $max;
    /**
     * @var string
     */
    public $message;

    /**
     * @param int $min
     * @param int $max
     * @param string $message
     */
    public function __construct($min = -1, $max= -1, $message = "")
    {
        assert(($min != -1 || $max != -1), "At least one of `min` or `max` must be specified");
        assert(($max == -1 || $min <= $max), "`min` cannot be more than `max`");
        $this->min = $min;
        $this->max = $max;
        $this->message = $message;
    }

    public function __invoke($form, Field $field)
    {
        if(!is_null($field->data)){
            $l = count($field->data);
        } else {
            $l = 0;
        }
        $message = $this->message;
        if($l < $this->min || $this->max != -1 && $l > $this->max){
          // TODO: Finish the validator
        }

    }
}
