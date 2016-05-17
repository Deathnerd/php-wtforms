<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/6/2016
 * Time: 4:55 PM
 */

namespace WTForms\Fields\Core;

/**
 * Encapsulate an ordered list of multiple instances of the same field type,
 * keeping data as a list.
 *
 * >>> $authors = new FieldList(new StringField("name", [new DataRequired()]));
 * @package WTForms\Fields\Core
 */
class FieldList extends Field
{
  /**
   * Field constructor.
   *
   * @param string $label
   * @param Field  $field
   * @param array  $options
   *
   * @throws \TypeError
   * @deprecated Not finished yet
   */
  public function __construct($label, Field $field, array $options = ['min_entries' => 0, 'max_entries' => null])
  {
    parent::__construct($label, $options);
    if ($this->filters) {
      throw new \TypeError("FieldList does not accept any filters. Instead, define them on the enclosed field");
    }
    $this->unbound_field = $field;
    $this->min_entries = $options['min_entries'];
    $this->max_entries = $options['max_entries'];
    $this->prefix = $options['prefix'] ?: '';
    $this->last_index = -1;
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
    $this->entries = [];
    if (is_null($data) || !$data) {
      if (is_callable($this->default)) {
        $data = $this->default->__invoke();
      } else {
        $data = $this->default;
      }
    }

    $this->object_data = $data;
    if ($formdata) {
      $indeces = sort(array_unique($this->extract_indeces($this->name, $formdata)));
      if ($this->max_entries) {
        $indeces = array_slice($indeces, 0, $this->max_entries);
      }

      foreach ($indeces as $index) {
        $oboj_data = current($data);

      }
    } else {
      foreach ($data as $obj_data) {
        $this->add_entry($formdata, $obj_data);
      }
    }

    while (count($this->entries) < $this->min_entries) {
      $this->add_entry($formdata);
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
  private function extract_indeces($prefix, $formdata)
  {
    $offset = strlen($prefix) + 1;
    $ret = [];
    foreach (array_keys($formdata) as $k) {
      if (strpos($k, $prefix)) {
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
   * @param array $formdata
   * @param null  $data
   * @param null  $index
   *
   * @return Field
   */
  private function add_entry($formdata = [], $data = null, $index = null)
  {
    assert(!$this->max_entries || count($this->entries) < $this->max_entries,
        "You cannot have more than max_entries entries in the FieldList");

    if ($index === null) {
      $index = $this->last_index + 1;
    }
    $name = "$this->short_name-$index";
    $id = "$this->id-$index";
    $field = $this->unbound_field;
    $field = new $field("", ["name" => $name, "id" => $id, "meta" => $this->meta, "prefix" => $this->prefix]);
    $field->process($formdata, $data);
    $this->entries[] = $field;

    return $field;
  }


}