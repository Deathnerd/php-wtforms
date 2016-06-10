<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/4/2016
 * Time: 11:15 AM
 */
namespace WTForms\Tests\SupportingClasses;

use WTForms\Fields\Core\Field;
use WTForms\Form;

class DummyField extends Field
{
    /**
     * @var array
     */
    public $errors;
    /**
     * @var mixed
     */
    public $data;
    /**
     * @var mixed
     */
    public $raw_data;

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->data = array_key_exists('data', $options) ? $options['data'] : null;
        $this->errors = array_key_exists('errors', $options) ? $options['errors'] : [];
        $this->raw_data = array_key_exists('raw_data', $options) ? $options['raw_data'] : null;
    }
}