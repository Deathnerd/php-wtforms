<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/6/2016
 * Time: 4:55 PM
 */

namespace WTForms\Fields\Core;

use WTForms\Exceptions\AssertionError;
use WTForms\Exceptions\IndexError;
use WTForms\Exceptions\TypeError;
use WTForms\Form;
use WTForms\Widgets\Core\ListWidget;

/**
 * Encapsulate an ordered list of multiple instances of the same field type,
 * keeping data as a list.
 *
 * >>> $authors = new FieldList(new StringField("name", [new DataRequired()]));
 * @package WTForms\Fields\Core
 */
class FieldList extends Field implements \Countable, \ArrayAccess
{
  /**
   * @var integer
   */
  public $min_entries = 0;
  /**
   * @var integer|null
   */
  public $max_entries = null;
  /**
   * @var Field
   */
  public $inner_field;
  /**
   * @var int
   */
  private $last_index = -1;

  /**
   * Field constructor.
   *
   * @param Form  $form
   * @param array $options
   *
   * @throws TypeError
   */
  public function __construct(array $options = [], Form $form = null)
  {
    if (!array_key_exists('inner_field', $options)) {
      throw new TypeError("FieldList requires an inner_field declaration");
    } elseif (!($options['inner_field'] instanceof Field)) {
      throw new TypeError(sprintf("FieldList requires an inner_field type subclassing Field; %s given", get_class($options['inner_field']) ?: gettype($options['inner_field'])));
    } else {
      $this->inner_field = $options['inner_field'];
      unset($options['inner_field']);
    }
    if (array_key_exists('filters', $options)) {
      throw new TypeError("FieldList does not accept any filters. Instead, define them on the enclosed field");
    }
    if (array_key_exists('min_entries', $options)) {
      $this->min_entries = $options['min_entries'];
      unset($options['min_entries']);
    }
    if (array_key_exists('max_entries', $options)) {
      $this->max_entries = $options['max_entries'];
      unset($options['max_entries']);
    }
    $options = array_merge(["widget" => new ListWidget()], $options);
    parent::__construct($options, $form);
    // unset the data attribute because it'll be 
    // overridden in the __get method to reflect the
    // entries property
    unset($this->data);
  }

  /**
   * Process incoming data, calling process_data, process_formdata as needed,
   * and run filters.
   *
   * If `data` is not provided, process_data will be called on the field's
   * default.
   *
   * Field subclasses usually won't override this, instead overriding the
   * process_formdata and process_data methods. Only override this for
   * special advanced processing, such as when a field encapsulates many
   * inputs.
   *
   * @param            $formdata
   * @param null|mixed $data
   */
  public function process($formdata, $data = null)
  {
    //@codeCoverageIgnoreStart
    $this->entries = [];
    if (is_null($data) || !$data) {
      if (is_callable($this->default)) {
        $data = $this->default->__invoke();
      } else {
        $data = $this->default;
      }
    }
    //@codeCoverageIgnoreEnd

    $this->object_data = $data;

    if ($formdata) {
      $indices = array_unique($this->extractIndices($this->name, $formdata));
      sort($indices);
      if ($this->max_entries) {
        $indices = array_slice($indices, 0, $this->max_entries);
      }

      $data_length = 0;
      // First add formdata that map to indices
      foreach ($indices as $index) {
        if ($data_length >= count($data)) {
          $obj_data = null;
        } else {
          $obj_data = $data[$data_length];
        }
        $this->addEntry($formdata, $obj_data, $index);
        $data_length++;
      }
    } else {
      // Or add data that maps to object data
      if (is_array($data)) {
        foreach ($data as $obj_data) {
          $this->addEntry($formdata, $obj_data);
        }
      }
    }
    // Pad out the entries until there is enough data
    // to hit the minimum entries limit
    while (count($this->entries) < $this->min_entries) {
      $this->addEntry($formdata);
    }
  }

  /**
   * Yield indices of any keys with given prefix.
   *
   * $formdata must be an object which will produce keys when iterated.
   *
   * @param $prefix
   * @param $formdata
   *
   * @return array
   */
  private function extractIndices($prefix, $formdata)
  {
    $offset = strlen($prefix) + 1;
    $ret = [];
    // Array keys are in the form of "prefix-numeric_index-subfield_name"
    foreach (array_keys($formdata) as $k) {
      if (strpos($k, $prefix) !== false) {
        $k = explode("-", substr($k, $offset), 2)[0];
        if (is_numeric($k)) {
          $ret[] = $k;
        }
      }
    }

    return $ret;
  }

  /**
   * Processes an unbound field and inserts it as a field type in this field list
   *
   * @param array        $formdata
   * @param mixed        $data
   * @param null|integer $index
   *
   * @return Field
   * @throws AssertionError
   */
  private function addEntry($formdata = [], $data = null, $index = null)
  {
    if (!(!$this->max_entries || count($this->entries) < $this->max_entries)) {
      throw new AssertionError("You cannot have more than max_entries entries in the FieldList");
    }

    if ($index === null) {
      $index = $this->last_index + 1;
    }
    $this->last_index = $index;
    $name = "$this->short_name-$index";
    $id = "$this->id-$index";

    $field = clone $this->inner_field;
    $field->meta = $this->meta;
    $field->prefix = $this->prefix;
    $field->name = $name;
    $field->id = $id;
    $field->process($formdata, $data);

    $this->entries[] = $field;

    return $field;
  }

  /**
   * Create a new entry with optional default data.
   *
   * Entries added in thi sway will *not* receive formdata however, and can
   * only receive object data
   *
   * @param mixed $data
   *
   * @return Field
   */
  public function appendEntry($data = null)
  {
    return $this->addEntry([], $data);
  }

  /**
   * Removes the last entry from the list and returns it
   * @return mixed
   * @throws IndexError
   */
  public function popEntry()
  {
    if (count($this->entries) == 0) {
      throw new IndexError;
    }
    $entry = array_pop($this->entries);
    $this->last_index -= 1;

    return $entry;
  }

  /**
   * Validate this FieldList.
   *
   * Note that the FieldList differs from normal field validation in
   * that FieldList validates all its enclosed fields first before running any
   * of its own validators.
   *
   * @param \WTForms\Form $form
   * @param array         $extra_validators
   *
   * @return bool|void
   */
  public function validate(Form $form, array $extra_validators = [])
  {
    $this->errors = [];

    // Run validators on all entries within
    foreach ($this->entries as $subfield) {
      /**
       * @var Field $subfield
       */
      if (!$subfield->validate($form, $extra_validators)) {
        $this->errors[] = $subfield->errors;
      }
    }
    $validators = $this->validators;
    foreach ($extra_validators as $validator) {
      $validators[] = $validator;
    }
    $this->runValidationChain($form, $validators);

    return count($this->errors) == 0;
  }

  /**
   * @inheritdoc
   */
  public function populateObj($obj, $name)
  {
    $values = $obj->$name;
    if (!is_array($values)) {
      $values = [];
    }
    $output = [];
    $i = 0;
    foreach ($this->entries as $field) {
      /**
       * @var Field $field
       */
      $fake_obj = new \stdClass();
      $fake_obj->data = @$values[$i];
      $field->populateObj($fake_obj, 'data');
      $output[] = $fake_obj->data;
      $i++;
    }
    $obj->$name = $output;
  }

  /**
   * @inheritdoc
   */
  public function __get($name)
  {
    if ($name == "data") {
      $ret = [];
      foreach ($this->entries as $entry) {
        $ret[] = $entry->data;
      }

      return $ret;
    }

    return parent::__get($name); //@codeCoverageIgnore
  }

  /**
   * @inheritdoc
   */
  public function count()
  {
    return count($this->entries);
  }

  /**
   * @codeCoverageIgnore
   */
  public function offsetExists($offset)
  {
    return isset($this->entries[$offset]);
  }

  /**
   * @codeCoverageIgnore
   */
  public function offsetGet($offset)
  {
    if (isset($this->entries[$offset])) {
      return $this->entries[$offset];
    }

    return null;
  }

  /**
   * @codeCoverageIgnore
   */
  public function offsetSet($offset, $value)
  {
    if (is_null($offset)) {
      $this->entries[] = $value;
    } else {
      $this->entries[$offset] = $value;
    }
  }

  /**
   * @codeCoverageIgnore
   */
  public function offsetUnset($offset)
  {
    unset($this->entries[$offset]);
  }
}
